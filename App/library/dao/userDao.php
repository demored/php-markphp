<?php

class userDao extends Dao {

	public function test(){

		$tmp = $this -> dao ->db ->get_all_sql('select * from t1');
//		$tmp = $this ->dao ->db ->get_row_sql('select * from big');
//		$tmp = $this -> dao ->db ->get_row(1,'big' , 'id');
//		$tmp = $this -> dao -> db -> get_count('big');
//		$tmp = $this -> dao ->db -> get_row_field(['id' => 1] , 'big');
//		$tmp = $this -> dao ->db -> get_all('big' ,'username', ['username'=>'BXO3EYIwgB4pESfrlcPp'],30 , 0);
//
//		$tmp = $this -> dao -> db ->delete_field('big' , ['id' => 1]);
//		$tmp = $this -> dao -> db ->delete('big' , 'id',2);
//		$tmp = $this -> dao->db->insert('big' , ['username' => '张三','group_id' => 20 , 'add_time' => time()]);
//		$tmp = $this -> dao ->db ->update_field('big' , ['group_id' => 32] , ['id'=>3 , 'group_id' => 31]);
//
//		$tmp = $this -> dao -> db ->insert_more('t1',[
//				['name' => '11'],
//				['name' => '12'],
//				['name' => '13']
//		]);
	}

	//设置cache
	public function test_cache(){
		//var_dump($this-> dao -> cache);
		//$this ->dao->cache('file')->set('name' , 'hello');
		//$this ->dao->cache('mem')->set('name' , 'hello');
		$this ->dao->nosql('redis')->set('name' , 'hello');
	}
}