var app = angular.module('filemanager', [

]);

app.controller('mainCtrl', function ($scope, $http, $timeout) {
    
    $scope.session = "";
    var session = window.sessionStorage.getItem('sessaoFM');

    $scope.loadon = function (msg) {
        var load = document.getElementById('loading');
        load.classList += " show-splash";
        load.querySelector("a").innerHTML = (msg == undefined) ? 'Carregando... Aguarde' : msg;
    }
    $scope.loadoff = function () {
        var load = document.getElementById('loading');
        load.classList = "main-splash";
    }
    
    $scope.obj = {
        usuario: '',
        senha: ''
    };

    if (session != null) {
        $scope.session = JSON.parse(session);
        listar(null);
    }

    $scope.lista = [];
    $scope.path = "";
    $scope.raiz = "";
    function listar (path) {

        $scope.loadon();

        // Simple GET request example:
        $http({
            method: 'POST',
            url: 'app.php',
            data: {
                metodo: 'listar',
                path: path
            }
        }).then(function successCallback(response) {
            $scope.loadoff();

            if (response.data.success) {
                $scope.lista = response.data.data;
                $scope.raiz = response.data.raiz;
                $scope.path = response.data.path;
                if ($scope.raiz!=$scope.path) {
                    $scope.lista.unshift({arquivo: '..', 'tipo':'goback'});
                }
            }

            
            setNavegations();
        }, function errorCallback(response) {
        });
    }

    $scope.acessar = function (obj) {
        $.ajax({
            url: "app.php",
            type: 'POST',
            dataType: 'json',
            data: {
                metodo: 'acessar',
                data: obj
            },
            success: function (e) {
                if (e.success) {
                    window.sessionStorage.setItem('sessaoFM', JSON.stringify(e.data));
                    window.location.reload();
                }else{
                    alert(e.msg);
                }
            }, 
            error: function (e) {
                console.error(e);
            }
          }).done(function() {
            $( this ).addClass( "done" );
          });
    }

    $scope.refresh = function () {
        listar ($scope.path);
    }

    $scope.itemClick = function (obj) {
        if (obj.tipo == 'dir') {
            listar ($scope.path + '/' + obj.arquivo);
        }
        if (obj.tipo == 'goback') {
            $scope.path = $scope.path.substr(0, $scope.path.lastIndexOf('/'));
            listar ($scope.path);
        }
    }

    // quando o usuário clica na foto para alterar
    $scope.anexar = function (obj) {
        // pega o input file
        var input = document.querySelector('#inputFile');
        input.click(); // força o evento click
    }

    $scope.fileNames = []; // exibir na tela
    $scope.filesCopy = [];
    // quando o usuário seleciona as imagens
    $scope.changeFiles = function (target) { // evento que escuta a escolha de arquivos

        // fazendo a copia dos arquivos
        // $scope.filesCopy = Array.prototype.slice.call(target.files);
        var files = Array.prototype.slice.call(target.files);
        
        // limpando o input file
        target.value = "";

        if ($scope.filesCopy.length > 0) {
            for (f of files) {
                var x = false;
                for (e of $scope.filesCopy) {
                    if (e.name == f.name) {
                        x = true;
                    }
                }
                if (!x) {
                    $scope.filesCopy.push(f);
                }
            };
            
        }else{
            for (f of files) $scope.filesCopy.push(f);
        }

        $timeout(function () {
            $scope.fileNames = [];
            for (f of $scope.filesCopy) $scope.fileNames.push(f.name);
        }, 100);
    }

    $scope.limpar = function () {
        // limpando os campos
        $scope.fileNames = []; // exibir na tela
        $scope.filesCopy = [];
    }

    $scope.upload = function () {

        // verificando se existem anexos
        if ($scope.filesCopy.length<=0) {
            return alert("Nenhum arquivo para enviar!");
        }

        var formData = new FormData();
        var i = 0;
        for (i=0;i<$scope.filesCopy.length;i++) {
            formData.append('file_'+i, $scope.filesCopy[i]);
        }
        // formData.append("files", JSON.stringify($scope.filesCopy));

        // limpando os campos
        $scope.fileNames = []; // exibir na tela
        $scope.filesCopy = [];

        // verificar se temos um cadastro ou uma edição
        formData.append('path', $scope.path);
        formData.append('metodo', "upload");

        var xhttp = new XMLHttpRequest();
        xhttp.onload = function (e) {
            if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                // se obteve sucesso na requisição
                if (response.success) {
                    MyToast.show('Arquivos enviados com sucesso.', 3);
                    listar ($scope.path);
                } else {
                    alert(response.msg);
                }
            }
        }
        var timeStart;
        xhttp.addEventListener("loadstart", function (e) {
            // var date = moment();
            // timeStart = moment().valueOf();
        });
        xhttp.addEventListener("loadend", function (e) {
            // var date = moment();
            // se o tempo de requisição for maior que 5 segundos 
            // if ((date.valueOf() - timeStart) > 5000) {
                // caso a conexão esteja lenta
                // MyToast.show('Conexão com o Servidor lenta.', 3);
            // }
        });

        xhttp.open('POST', "app.php", true);
        xhttp.setRequestHeader('X-Requested-With', 'XMLHttpRequest'); // configurando o cabeçalho da requisição

        xhttp.send(formData);
    }

    $scope.deletarArquivo = function (obj) {
        if (obj.tipo = 'file') {
            
            if (window.confirm("Confirma a remoção do arquivo " + obj.arquivo + "?")) {
                // Simple GET request example:
                $http({
                    method: 'POST',
                    url: 'app.php',
                    data: {
                        metodo: 'deletar',
                        path: $scope.path + '/' + obj.arquivo
                    }
                }).then(function successCallback(response) {
                    if (response.data.success) {
                        MyToast.show('O arquivo '+ obj.arquivo +' foi removido.', 3);
                        listar ($scope.path);
                    }
                }, function errorCallback(response) {
                });
            }
        }
    }

    $scope.sair = function () {
        window.sessionStorage.removeItem('sessaoFM');
        window.location.reload();
    }

    $scope.navegation = [{name: 'raiz', path: $scope.path}];
    function setNavegations () {
        var nav = $scope.path.replace($scope.raiz, '');
        var split = nav.split('/');

        // reecrevendo
        $scope.navegation = [{name: 'raiz', path: $scope.raiz}];

        for (f of split) {
            if (f!=''){
                var pathSplit = $scope.path.split('/');
                var i = pathSplit.indexOf(f);
                var path = '';
                for (p of pathSplit) {
                    if (p != f) {
                        path += p + '/';
                    }else{
                        break;
                    }
                }
                
                $scope.navegation.push({
                    name: f,
                    path: (path + f)
                });
            }
        }
    }

    $scope.navegar = function (nav) {
        listar(nav.path);
    }
});