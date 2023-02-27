<?php 

namespace App\Controllers;

// recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action{

	public function timeline(){

		$this->validaAutenticacao();

		//recupeação dos tweets
		$tweet = Container::getModel('Tweet');

		$tweet->__set('id_usuario', $_SESSION['id']);		

		$total_registros_pag = 5;
		$pag = isset($_GET['pag']) ? $_GET['pag'] : 1;
		$deslocamento = ($pag - 1) * $total_registros_pag;

		//$tweets = $tweet->getAll();
		$tweets = $tweet->getPorPagina($total_registros_pag, $deslocamento);
		$total_tweets = $tweet->getTotalRegistros();
		$this->view->total_de_pag = ceil($total_tweets['total']/$total_registros_pag);
		$this->view->pag_ativa = $pag;

		$this->view->tweets = $tweets;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

		$this->render('timeline');
				
	}

	public function tweet(){

		$this->validaAutenticacao();
			
		$tweet = Container::getModel('Tweet');

		$tweet->__set('tweet', $_POST['tweet']);
		$tweet->__set('id_usuario', $_SESSION['id']);

		$tweet->salvar();

		header('Location: /timeline');		
		
	}

	public function remove_tweet(){

		$this->validaAutenticacao();

		$id_tweet = isset($_GET['id_tweet']) ? $_GET['id_tweet'] : '';
		$id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		
		$usuario->removerTweet($id_tweet, $id_usuario);

		header('Location: /timeline');			
		
	}

	public function validaAutenticacao(){

		session_start();

		if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
			header('Location: /?login=erro');
		}
	}

	public function quemSeguir(){

		$this->validaAutenticacao();

		$pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : ''; 

		$usuarios = array();

		if ($pesquisarPor != '') {
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $pesquisarPor);
			$usuario->__set('id', $_SESSION['id']);
			$usuarios = $usuario->getAll();
		}

		$this->view->usuarios = $usuarios;

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		$this->view->info_usuario = $usuario->getInfoUsuario();
		$this->view->total_tweets = $usuario->getTotalTweets();
		$this->view->total_seguindo = $usuario->getTotalSeguindo();
		$this->view->total_seguidores = $usuario->getTotalSeguidores();

		$this->render('quemSeguir');
	}

	public function acao(){

		$this->validaAutenticacao();

		$acao = isset($_GET['acao']) ? $_GET['acao'] : '';
		$id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

		$usuario = Container::getModel('Usuario');
		$usuario->__set('id', $_SESSION['id']);

		if ($acao == 'seguir') {
			$usuario->seguirUsuario($id_usuario_seguindo);
		}else if ($acao == 'deixar_de_seguir'){
			$usuario->deixarSeguirUsuario($id_usuario_seguindo);
		}

		header('Location: /quem_seguir');

	}

}

?>