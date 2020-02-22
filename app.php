<?php

    $raiz = $_SERVER['DOCUMENT_ROOT'] . '/meu_projeto'; // pasta do projeto, caso seja em localhost

    if(!$_POST){ $_POST =  file_get_contents ( "php://input" ); }
    if (gettype($_POST) != "array") $_POST = json_decode ($_POST, true);

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
        if (!file_exists($path)) $path = $raiz;

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

        echo json_encode($resp);
    }

    function upload ($raiz) {
        ini_set("post_max_size", "1024M");
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        ini_set("upload_max_filesize", "1024M");
        // date_default_timezone_set("Brazil/East"); //Definindo timezone padrão

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
            $response['msg'] .= 'Ocorreu um erro ' . $e;
            $totalError++;
        }

        $resp['success'] = true;

        echo json_encode($resp);
    }

    function deletar () {

        $resp = array("success"=>false, "msg"=>"", "data"=>"");

        $path = $_POST['path'];
        unlink($path);

        $resp['success'] = true;

        echo json_encode($resp);
    }
    
?>