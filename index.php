<?php

ini_set('log_errors','on'); //ログを取るか
ini_set('error_log','php.log'); // ログの出力先の指定
session_start(); //セッションを使う

//登場人物格納用
$guys = array();

//性別クラス
class Sex {
	const MAN = 1;
	const WOMAN = 2;	
}


//人クラス 抽象クラス
abstract class Human {
	protected $name;
	protected $sex;

	abstract public function say();

	public function setName($str) {
		$this->name = $str;
	}
	public function getName(){
		return $this->name;
	}
	public function getSex(){
		return $this->sex;
	}
	public function setSex($num){
		$this->sex = $num;
	}
}

//自分クラス
class Myself extends Human{
	//プロパティ
	protected $hp;
	protected $attackMin;
	protected $attackMax;

	public function __construct($name, $sex, $hp, $attackMin, $attackMax){
		$this->name = $name;
		$this->sex = $sex;
		$this->hp = $hp;
		$this->attackMin = $attackMin;
		$this->attackMax = $attackMax;
	}
	
	public function say(){
		History::set($this->name.'が叫ぶ！');
		History::set('ぐぐっ！！');

	}
		
	public function setHp($num){
		$this->hp = $num;
	}

	public function getHp(){
		return $this->hp;
	}

	public function attack($targetObj){
		$attackPoint = mt_rand($this->attackMin, $this->attackMax);
		if(!mt_rand(0,9)){
			$attackPoint = $attackPoint * 1.5;
			$attackPoint = (int)$attackPoint;
			History::set($this->getName().'クリティカルヒット');
		}
			$targetObj->setHp($targetObj->getHp()-$attackPoint);
			History::set($attackPoint.'ポイントのダメージ');
	}
}

// 従業員クラス
class Employer extends Myself {
	protected $img;
	//コンストラクタ
	public function __construct($name, $sex, $hp, $img, $attackMin, $attackMax){
	parent::__construct($name, $sex, $hp, $attackMin, $attackMax);
	$this->img = $img;
	}

	public function getImg(){
    return $this->img;
  	}

	public function say(){
	    switch(mt_rand(0,2)){
      		case 0 :
        		History::set('ぐはぁ！');
        		break;
      		case 1 :
        		History::set('うおーっ！');
        		break;
      		case 2 :
        		History::set('Oops！');
        		break;
    	}
    }
}

class Women extends Human{
	private $recoverhp;
	private $img;

	public function __construct($name, $sex, $img, $recoverhp){
		$this->name = $name;
		$this->sex = $sex;
		$this->img = $img;
		$this->recoverhp = $recoverhp;
	}

	public function getImg(){
    return $this->img;
  	}

	public function say(){
		History::set($this->name.'が言った！');
		History::set('お疲れ様です！');
	}

	public function recover($targetObj){
		$recoverPoint = $this->recoverhp;
		$targetObj->setHp($targetObj->getHp()+$recoverPoint);
		History::set($recoverPoint.'ポイント回復した！');	
	}
}

interface HistoryInterface{
	public static function set($str);
	public static function clear();
}

class History implements HistoryInterface{
	public static function set($str){
		//セッションHistoryが作られていなければ作る
		if(empty($_SESSION['history'])) $_SESSION['history'] = '';
		//文字列をセッションHistoryへ格納
		$_SESSION['history'] .= $str.'<br>';
	}
	public static function clear(){
		unset($_SESSION['history']);
	}
}

//インスタンス生成
$myself = new Myself('自分',Sex::MAN, 500, 40, 120);
$employers[] = new Employer('社長', Sex::MAN, 800, 'img/emp01.png', 50, 100); 
$employers[] = new Employer('上司', Sex::MAN, 500, 'img/emp02.png', 50, 100); 
$employers[] = new Employer('同僚', Sex::MAN, 500, 'img/emp03.png', 40, 100); 
$employers[] = new Employer('後輩', Sex::MAN, 500,'img/emp04.png', 30, 100); 
$employers[] = new Employer('筋肉系エンジニア', Sex::MAN, 1000,'img/emp05.png', 80, 150); 
$employers[] = new Women ('事務員', Sex::WOMAN, 'img/wo01.png', mt_rand(60, 1000));
$employers[] = new Women ('清掃員', Sex::WOMAN, 'img/wo02.png', mt_rand(60, 1000));

function createEmployers() {
	global $employers;
	$employer = $employers[mt_rand(0,6)];
	History::Set($employer->getName().'が現れた！');
	$_SESSION['employer'] = $employer;
	
	// global $getsex;
	// $getsex = $employer->getSex();
	// History::Set($getsex.'が現れた！');

}

function createHuman() {
	global $myself;
	$_SESSION['human'] = $myself;
}

function init(){
	History::clear();
	History::set('初期化します！');
	$_SESSION['knockDownCount'] = 0 ;
	createHuman();
	createEmployers();
}

function gameOver() {
	$_SESSION = array();
}

//POST送信されていた場合
if(!empty($_POST['first'])){
	init();
}

	


if(!empty($_POST)){

	$attacklFlg = (!empty($_POST['attack'])) ? true : false;
	$startFlg = (!empty($_POST['start'])) ? true : false;
	error_log('POSTされた！');

	// var_dump($attacklFlg);
	// var_dump($startFlg);

	if($startFlg){
		History::set('ゲームスタート！');
		init();
	}else{


		//攻撃するを押した場合
		if($attacklFlg){

			//モンスターに攻撃を与える
			History::set($_SESSION['human']->getName().'の攻撃！');
			$_SESSION['human']->attack($_SESSION['employer']);
			$_SESSION['employer']->say();

			//モンスターが攻撃する
			History::set($_SESSION['employer']->getName().'の攻撃！');
			$_SESSION['employer']->attack($_SESSION['human']);
			$_SESSION['human']->say();

			//自分のHpが０になったらゲームオーバー
			if($_SESSION['human']->getHp() <= 0){
				gameOver();
			}else{
				//HPが０以下になったら他のモンす出現させる
				if($_SESSION['employer']->getHp() <= 0){
					History::set($_SESSION['employer']->getName().'を倒した！');
					createEmployers();
					$_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
				}
			}
		}else{ //逃げるを押した場合
			
			if(empty($_POST['first'])){
			
				//emoloyerがWomenだったら
				if($_SESSION['employer']->getSex() == 2){
				$_SESSION['employer']->say();
				$_SESSION['employer']->recover($_SESSION['human']);				
				}

				History::set('やめといた！');
				createEmployers();			
			}

			
		}
	}
	$_POST = array();
}


?>




<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="style.css">
	<title>ゲーム</title>
</head>
<body>
　<h1>たたかえ！会社員！</h1>

	<div class="main">
		<?php if(empty($_SESSION)){ ?>
			<h2>GAME START!!!</h2>
			<form method="post">
				<input type="submit" name="first" value=">>GameStart">
			</form>
		<?php }else{ ?>
			<h2><?php echo $_SESSION['employer']->getName(). 'が現れた！';
			 ?></h2>
			<div class="pic">
				<img src="<?php echo $_SESSION['employer']->getImg(); ?>">
			</div>

			<div class="pic2" style="display: none;">  
				 <!-- style="display: none;"  -->
				<img src="img/inazuma.png">
			</div>

			
			<?php if($_SESSION['employer']->getSex() == 1){ ?>
			<p><?php echo $_SESSION['employer']->getName() ?>のHP：<?php echo $_SESSION['employer']->getHp(); ?></p>
			<?php }else{ ?>
				<p><?php echo $_SESSION['employer']->getName() ?>のHP：♡</p>
			<?php } ?>
			
			<p>倒した数：<?php echo $_SESSION['knockDownCount']; ?></p>
			<p>自分の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
			<form method="post" id='mainform'>
				
				<?php if($_SESSION['employer']->getSex() == 1){ ?>
				<input type="submit" name="attack" class="btn" value="▶たたかう">
				<?php } ?>


				<input type="submit" name="escape" value="▶やめとく">
				<input type="submit" name="start" value="▶ゲームリスタート">
				
			</form>

			<div class="log">
			<!-- <p>履歴の表示</p> -->
			<p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
			</div>
		<?php } ?>

		

	</div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script src="main.js"></script>







</body>
</html>