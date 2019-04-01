<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CI_Controller {

	public function index()
	{
		die('No direct script access allowed');
	}
	// MENU //
	public function get_menu()
	{
		$where['id'] = post('id') ? post('id') : 0;
		$json['result'] = db_get('sim_menu',$where);
		show_json($json);
	}
	public function datatable_menu()
	{
		$start = post('start') ? post('start') : 0;
		$limit = post('length') ? post('length') : 10;
		$search = post('search') ? post('search')['value'] : '';
		$likes['menu'] = $search;
		$menu = db_search('sim_menu',$start,$limit,[],$likes);
		$json = [];
		foreach ($menu as $i => $v) {
			$item = [];
			array_push($item,($i+1));
			array_push($item,$v->menu);
			array_push($item,$v->icon);
			array_push($item,button_aksi_menus_submenus($v->id));
			array_push($json,$item);
		}
		$result['draw'] = post('draw');
		$result['recordsTotal'] = db_count('sim_menu');
		$result['recordsFiltered'] = count($menu);
		$result['data'] = $json;
		show_real_json($result);
	}
	// SUBMENU //
	public function get_submenu()
	{
		$where['id'] = post('id') ? post('id') : 0;
		$json['result'] = db_get('sim_submenu',$where);
		show_json($json);
	}
	public function datatable_submenu()
	{
		$start = post('start') ? post('start') : 0;
		$limit = post('length') ? post('length') : 10;
		$search = post('search') ? post('search')['value'] : '';
		$likes['submenu'] = $search;
		$likes['url'] = $search;
		$submenu = db_search('sim_submenu',$start,$limit,[],$likes);
		$json = [];
		foreach ($submenu as $i => $v) {
			$item = [];
			array_push($item,($i+1));
			array_push($item,$v->submenu);
			array_push($item,$v->url);
			array_push($item,button_aksi_menus_submenus($v->id));
			array_push($json,$item);
		}
		$result['draw'] = post('draw');
		$result['recordsTotal'] = db_count('sim_submenu');
		$result['recordsFiltered'] = count($submenu);
		$result['data'] = $json;
		show_real_json($result);
	}
	// HAK AKSES //
	public function get_hak_akses()
	{
		$where['id'] = post('id') ? post('id') : 0;
		$json['result'] = db_get('sim_level',$where);
		show_json($json);
	}
	public function datatable_hak_akses()
	{
		$start = post('start') ? post('start') : 0;
		$limit = post('length') ? post('length') : 10;
		$search = post('search') ? post('search')['value'] : '';
		$likes['level'] = $search;
		$hak_akses = db_search('sim_level',$start,$limit,[],$likes);
		$json = [];
		foreach ($hak_akses as $i => $v) {
			$item = [];
			array_push($item,($i+1));
			array_push($item,$v->level);
			array_push($item,button_aksi_default($v->id));
			array_push($json,$item);
		}
		$result['draw'] = post('draw');
		$result['recordsTotal'] = db_count('sim_level');
		$result['recordsFiltered'] = count($hak_akses);
		$result['data'] = $json;
		show_real_json($result);
	}
	// MENU //
	public function get_users()
	{
		$where['id'] = post('id') ? post('id') : 0;
		$json['result'] = db_get('sim_users',$where);
		show_json($json);
	}
	public function datatable_users()
	{
		$start = post('start') ? post('start') : 0;
		$limit = post('length') ? post('length') : 10;
		$search = post('search') ? post('search')['value'] : '';
		$likes['username'] = $search;
		$likes['nama'] = $search;
		$users = db_search('sim_users',$start,$limit,[],$likes);
		$json = [];
		foreach ($users as $i => $v) {
			$item = [];
			array_push($item,($i+1));
			array_push($item,get_level($v->id_level));
			array_push($item,$v->username);
			array_push($item,$v->nama);
			array_push($item,button_aksi_users($v->id));
			array_push($json,$item);
		}
		$result['draw'] = post('draw');
		$result['recordsTotal'] = db_count('sim_users');
		$result['recordsFiltered'] = count($users);
		$result['data'] = $json;
		show_real_json($result);
	}
	// Navigasi //
	public function show_menu()
	{
		$id_level = post('id_level') ? post('id_level') : 0;
		$result = [];
		$data_menu = db_show('sim_menu');
		foreach ($data_menu as $menu) {
			if (!db_cek('sim_tmenu',['id_level'=>$id_level,'id_menu'=>$menu->id])) {
				array_push($result,db_get('sim_menu',['id'=>$menu->id]));
			}
		}
		$json['result'] = $result;
		show_json($json);
	}
	public function show_submenu()
	{
		$id_level = post('id_level') ? post('id_level') : 0;
		$id_tmenu = post('id_tmenu') ? post('id_tmenu') : 0;
		$id_menu = post('id_menu') ? post('id_menu') : 0;
		$result = [];
		$data_submenu = db_show('sim_submenu');
		foreach ($data_submenu as $submenu) {
			if (!db_cek('sim_tsubmenu',['id_level'=>$id_level,'id_tmenu'=>$id_tmenu,'id_menu'=>$id_menu,'id_submenu'=>$submenu->id])) {
				array_push($result,db_get('sim_submenu',['id'=>$submenu->id]));
			}
		}
		$json['result'] = $result;
		show_json($json);
	}

}