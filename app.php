<?php

    // sinalize aqui o nome da pasta onde está o seu projeto
    // caso seja encontrado o sistema navegará até a raiz do projeto
    $raiz = $_SERVER['DOCUMENT_ROOT'] . '/vwa';
    if (!file_exists($raiz)) $raiz = $_SERVER['DOCUMENT_ROOT'];

    if(!$_POST){ $_POST =  file_get_contents ( "php://input" ); }
    if (gettype($_POST) != "array") $_POST = json_decode ($_POST, true);

    ini_set("post_max_size", "1024M");
    ini_set('memory_limit', '-1');
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    ini_set("upload_max_filesize", "1024M");
    // date_default_timezone_set("Brazil/East"); //Definindo timezone padrão

    $_POST['metodo']($raiz);

    function acessar ($raiz) {
        // usuário e senha padrão
        $user = "admin";
        $pass = "admin";
        
        $data = $_POST['data'];

        $resp = array("success"=>false, "msg"=>"Acesso Negado, Usuário ou senha inválidos!", "data"=>"");

        // comparando as senhas
        if ($data['usuario'] === $user && $data['senha'] === $pass) {
            $resp['success'] = true;
            $resp['msg'] = "autenticado";
            $resp['data'] = array("usuario" => $data['usuario'], "autenticado" => true, "path" => $raiz);
        }

        echo json_encode($resp);
    }

    function listar ($raiz) {
        $path = $raiz;
        if (!empty($_POST['path'])) $path = $_POST['path'];
        if (!file_exists($path)) $path = $_SERVER['DOCUMENT_ROOT'];

        $resp = array("success"=>false, "msg"=>"", "data"=>"");

        $files = scandir($path);
        
        // separando pastas de arquivos
        $a = array(); // arquivos
        $p = array(); // pastas
        foreach($files as $key) {
            // removendo ocultos
            if ($key[0] != ".") {
                // pegando arquivos
                if (strrpos($key, ".") != false) {
                    array_push($a, array("arquivo" => $key, "tipo" => "file"));
                // pegando pastas
                }else{
                    array_push($p, array("arquivo" => $key, "tipo" => "dir"));
                }
            }
        }
        // mesclando pastas e arquivos
        $files = array_merge($p, $a);

        $resp['success'] = true;
        $resp['path'] = $path;
        $resp['raiz'] = $raiz;
        $resp['data'] = $files;
        $resp['limits'] = getLimitsUpload();

        echo json_encode($resp);
    }

    function getLimitsUpload () {
        $limits = array(
            "max_file_uploads" => ini_get('max_file_uploads'),
            "upload_max_filesize" => ini_get('upload_max_filesize'),
            "post_max_size" => ini_get('post_max_size')
        );

        return $limits;
    }

    function upload ($raiz) {

        $resp = array("success"=>false, "msg"=>"", "data"=>"");

        try {

            // enviando arquivos
            if (!empty($_FILES)) {
                
                $path = $raiz;
                if (!empty($_POST['path'])) $path = $_POST['path'];
                $target_dir = $path;
                
                foreach ($_FILES as $key) {
                    $target_file = $target_dir . '/' . basename($key["name"]);
                    //Fazer upload do arquivo
                    if (!move_uploaded_file($key['tmp_name'], $target_file)) {
                        $resp["msg"] = "Ocorreu um erro ao tentar enviar";
                        $resp["success"] = false;
                        die (json_encode($resp));
                    } 
                }
            }

        }catch(Exception $e){
            $resp['success'] = false;
            $resp['msg'] .= 'Ocorreu um erro ' . $e;
            die(json_encode($resp));
        }

        $resp['success'] = true;

        echo json_encode($resp);
    }

    function downloadArquivo () {
        $resp = array("success"=>false, "msg"=>"", "data"=>"");
    }

    function deletar () {

        $resp = array("success"=>false, "msg"=>"", "data"=>"");

        $path = $_POST['path'];
        unlink($path);

        $resp['success'] = true;

        echo json_encode($resp);
    }
    
    function criarDiretorio () {
        $path = $_POST['path'];

        $resp = array("success"=>false, "msg"=>"", "data"=>"");

        try {

            if (empty($path)) {
                $resp['success'] = false;
                $resp['msg'] = "Houve um erro no caminho especificado.";
                die(json_encode($resp));
            }

            // criamos o diretório especifícado
            mkdir($path, 0777, true);
            chmod($path, 0777);

        }catch(Exception $e){
            $resp['success'] = false;
            $resp['msg'] .= 'Ocorreu um erro ' . $e;
            die(json_encode($resp));
        }

        $resp['success'] = true;
        $resp['msg'] = "Diretório criado com sucesso.";

        echo json_encode($resp);
    }

    function deletarDiretorio () {
        $path = $_POST['path'];

        try {

            if (empty($path)) {
                $resp['success'] = false;
                $resp['msg'] = "Houve um erro no caminho especificado.";
                die(json_encode($resp));
            }

            // removendo a pasta, subpastas e arquivos de modo recursirvo
            function _delTree($dir) {
                $files = array_diff(scandir($dir), array('.','..'));
                foreach ($files as $file) {
                (is_dir("$dir/$file")) ? _delTree("$dir/$file") : unlink("$dir/$file");
                }
                return rmdir($dir);
            }
            _delTree($path);

        }catch(Exception $e){
            $resp['success'] = false;
            $resp['msg'] .= 'Ocorreu um erro ' . $e;
            die(json_encode($resp));
        }

        $resp['success'] = true;
        $resp['msg'] = "Diretório criado com sucesso.";

        echo json_encode($resp);
    }
?>