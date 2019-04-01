<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// DATABASE //

function db_fields($table)
{
	$CI =& get_instance();
	return $CI->db->list_fields($table);
}
function fields_table($table,$exclude=[])
{
	$CI =& get_instance();
	$output = [];
	$fields = $CI->db->list_fields($table);
	foreach ($fields as $field) {
		if (in_array($field,$exclude) == false) {
			array_push($output,$field);
		}
	}
	return $output;
}
function db_cek($table,$where)
{
	$CI =& get_instance();
	$CI->db->where($where);
	$rows = $CI->db->get($table)->num_rows();
	if ($rows > 0) {
		return true;
	}
	return false;
}
function db_get($table,$where)
{
	$CI =& get_instance();
	$CI->db->where($where);
	return $CI->db->get($table)->row();
}
function db_search($table=null,$start=0,$limit=10,$wheres=[],$likes=[],$order_by=''){
	$CI =& get_instance();
	$query = "SELECT * FROM `".$table."`";

	if (!empty($wheres) || !empty($likes)) {
		$query .= " WHERE";
	}
	$no=0;
	foreach ($wheres as $key => $value) {
		if ($no==0) {
			$query .= " `".$key."` = '".$value."'";
		} else {
			$query .= " AND `".$key."` = '".$value."'";
		}
		$no++;
	}
	$arr_like = null;
	$no=0;
	foreach ($likes as $key => $value) {
		if ($no==0) {
			$arr_like .= " `".$key."` LIKE '%".$value."%'";
		} else {
			$arr_like .= " OR `".$key."` LIKE '%".$value."%'";
		}
		$no++;
	}
	if (!empty($wheres) AND !empty($likes)) {
		$query .= " AND";
	}
	if (!empty($likes)) {
		if (sizeof($arr_like) > 0) {
			$query .= " (".$arr_like.")";
		}
	}
	if ($order_by != '') {
		$query .= " ORDER BY `".$order_by."` DESC";
	}
	$query .= " LIMIT ".$start.",".$limit;
	return $CI->db->query($query)->result();
}
function db_show($table,$where=[])
{
	$CI =& get_instance();
	$CI->db->where($where);
	return $CI->db->get($table)->result();
}
function db_count($table,$where=[])
{
	$CI =& get_instance();
	$CI->db->where($where);
	return $CI->db->get($table)->num_rows();
}
function db_insert($table,$value)
{
	$CI =& get_instance();
	if ($CI->db->insert($table,$value)) {
		return true;
	}
	return false;
}
function db_update($table,$set,$where)
{
	$CI =& get_instance();
	$CI->db->where($where);
	if ($CI->db->update($table,$set)) {
		return true;
	}
	return false;
}
function db_delete($table,$where)
{
	$CI =& get_instance();
	$CI->db->where($where);
	if ($CI->db->delete($table,$where)) {
		return true;
	}
	return false;
}

// UTILITY //

function cek_validasi($fields)
{
	$CI =& get_instance();
	foreach ($fields as $value) {
		$CI->form_validation->set_rules($value,$value,'trim|required');
	}
	if ($CI->form_validation->run()) {
		return true;
	}
	return false;
}
function post($name,$securing=TRUE)
{
	$CI =& get_instance();
	return $CI->input->post($name,$securing);
}

// NAVIGASI //

function show_tmenu($id_level=0)
{
	return db_show('sim_tmenu',['id_level'=>$id_level]);
}
function show_tsubmenu($id_level=0,$id_menu=0)
{
	return db_show('sim_tsubmenu',['id_level'=>$id_level,'id_menu'=>$id_menu]);
}
function get_level($id_level=0)
{
	return db_get('sim_level',['id'=>$id_level])->level;
}
function get_menu($id_menu=0)
{
	return db_get('sim_menu',['id'=>$id_menu]);
}
function get_submenu($id_submenu=0)
{
	return db_get('sim_submenu',['id'=>$id_submenu]);
}
function submenu_href($id_submenu=0)
{
	$CI =& get_instance();
	$submenus = db_get('sim_submenu',['id'=>$id_submenu]);
	return site_url(str_replace(["/"],".",$submenus->url));
}
function simenara_tmenus()
{
	$CI =& get_instance();
	if (cek_login_user()) {
		$CI->db->order_by('urutan','asc');
		$where['id_level'] = $CI->session->userdata('users')->id_level;
		return $CI->db->get_where('sim_tmenu',$where)->result();
	} else {
		redirect('dasbor.login');
	}
}
function simenara_tsubmenus($id_tmenu)
{
	$CI =& get_instance();
	if (cek_login_user()) {
		$CI->db->order_by('urutan','asc');
		$where['id_tmenu'] = $id_tmenu;
		$where['id_level'] = $CI->session->userdata('users')->id_level;
		return $CI->db->get_where('sim_tsubmenu',$where)->result();
	} else {
		redirect('dasbor/login');
	}
}

// VIEWS //

function views($view_name,$data=[])
{
	$CI =& get_instance();
	$CI->load->view('backend/header_view',$data);
	$CI->load->view('backend/'.$view_name,$data);
	$CI->load->view('backend/footer_view',$data);
}
function show_json($result=[])
{
	$CI =& get_instance();
	$CI->output
		->set_header('Access-Control-Allow-Origin:*')
		->set_status_header(200)
		->set_content_type('application/json','utf-8')
		->set_output(json_encode(['response'=>$result],JSON_PRETTY_PRINT))
		->_display();
	exit;
}
function show_real_json($result=[])
{
	$CI =& get_instance();
	$CI->output
		->set_header('Access-Control-Allow-Origin:*')
		->set_status_header(200)
		->set_content_type('application/json','utf-8')
		->set_output(json_encode($result,JSON_PRETTY_PRINT))
		->_display();
	exit;
}
function str_flags($flags=0)
{
	if ($flags>0) {
		return "Ya";
	}
	return "Tidak";
}

// SESSION //

function cek_login_user()
{
	$CI =& get_instance();
	if ($CI->session->userdata('is_logged')) {
		return true;
	}
	return false;
}
function login_required()
{
	if (!cek_login_user()) {
		redirect('dasbor.login');
	}
}
function get_session($item=NULL)
{
	$CI =& get_instance();
	return $CI->session->userdata($item);
}
function set_session($item)
{
	$CI =& get_instance();
	$CI->session->set_userdata($item);
}
function session_users()
{
	$CI =& get_instance();
	if (cek_login_user()) {
		return $CI->session->userdata('users');
	} else {
		redirect('dasbor.login');
	}
}

// ALERT //

function html_tag_script($html='')
{
	return '<script>'.$html.'</script>';
}
function html_tag_script_onready($html='')
{
	return '<script type="text/javascript">$(document).ready(function(){'.$html.'});</script>';
}
function set_alert($message='',$typem=FALSE)
{
	$CI =& get_instance();
	$type = ($typem) ? 'success':'error';
	$title = ($typem) ? 'Berhasil':'Gagal';
	$html = html_tag_script_onready("new PNotify({title:'".$title."',text:'".$message."',type:'".$type."',styling:'bootstrap3'});");
	return $CI->session->set_flashdata('alert',$html);
}
function show_alert()
{
	$CI =& get_instance();
	return $CI->session->flashdata('alert');
}

// URL //

function assets($path)
{
	return base_url('assets/'.$path);
}
function ajax($method='')
{
	return site_url('ajax.'.$method);
}
function image_upload($filename='')
{
	if($filename != ''){
		$src = base_url('uploads/images/'.$filename);
		return '<img src="'.$src.'" class="img-responsive">';
	} else {
		return 'NULL';
	}
}
function upload_image($name="")
{
	$CI =& get_instance();
	$config['upload_path'] = './uploads/images/';
	$config['allowed_types'] = 'gif|jpg|png';
	$config['max_size']  = 0;
	$config['max_width']  = 0;
	$config['max_height']  = 0;
	$config['encrypt_name'] = TRUE;
	$CI->load->library('upload',$config);
	$CI->upload->initialize($config);
	if (!$CI->upload->do_upload($name)) {
		return false;
	} else {
		return $CI->upload->data();
	}
}

// Button //

function button_aksi_default($id)
{
	$btn_edit['type'] = 'button';
	$btn_edit['class'] = 'btn btn-info btn-xs';
	$btn_edit['onclick'] = 'modal_edit('.$id.')';
	$btn_edit['content'] = '<i class="fa fa-edit"></i> Edit';
	$btn_hapus['type'] = 'button';
	$btn_hapus['class'] = 'btn btn-danger btn-xs';
	$btn_hapus['onclick'] = 'modal_hapus('.$id.')';
	$btn_hapus['content'] = '<i class="fa fa-trash"></i> Hapus';
	return form_button($btn_edit).form_button($btn_hapus);
}
function button_aksi_users($id)
{
	$btn_edit['type'] = 'button';
	$btn_edit['class'] = 'btn btn-info btn-xs';
	$btn_edit['onclick'] = 'modal_edit('.$id.')';
	$btn_edit['content'] = '<i class="fa fa-edit"></i> Edit';
	$btn_reset['type'] = 'button';
	$btn_reset['class'] = 'btn btn-warning btn-xs';
	$btn_reset['onclick'] = 'modal_reset('.$id.')';
	$btn_reset['content'] = '<i class="fa fa-key"></i> Reset Password';
	$btn_hapus['type'] = 'button';
	$btn_hapus['class'] = 'btn btn-danger btn-xs';
	$btn_hapus['onclick'] = 'modal_hapus('.$id.')';
	$btn_hapus['content'] = '<i class="fa fa-trash"></i> Hapus';
	return form_button($btn_edit).form_button($btn_reset).form_button($btn_hapus);
}
function button_aksi_menus_submenus($id)
{
	$btn_edit['type'] = 'button';
	$btn_edit['class'] = 'btn btn-info btn-xs';
	$btn_edit['onclick'] = 'modal_edit('.$id.')';
	$btn_edit['content'] = '<i class="fa fa-edit"></i> Edit';
	$btn_hapus['type'] = 'button';
	$btn_hapus['class'] = 'btn btn-danger btn-xs';
	$btn_hapus['onclick'] = 'modal_hapus('.$id.')';
	$btn_hapus['content'] = '<i class="fa fa-trash"></i> Hapus';
	return form_button($btn_edit).form_button($btn_hapus);
}
function date_now()
{
	return date("Y-m-d");
}
function datetime_now()
{
	return date("Y-m-d H:i:s");
}
function date_fix($date)
{
	return date("Y-m-d",strtotime($date));
}
function datetime_fix($date)
{
	return date("Y-m-d H:i:s",strtotime($date));
}
// FORM //
function generate_form_group($table,$type,$hidden=[])
{
	$html = '';
	$CI =& get_instance();
	$fields = $CI->db->field_data($table);
	foreach ($fields as $field) {
		if ($field->primary_key) {
			if ($type != 'insert' || $type != 'tambah') {
				$html .= '<input type="hidden" id="'.$type.'_'.$field->name.'" name="'.$field->name.'">';
			}
		} else {
			if (count($hidden)>0) {
				if (in_array($field->name,$hidden) != false) {
					$html .= form_input(['type'=>'hidden','id'=>$type.'_'.$field->name,'name'=>$field->name]);
				} else {
					$html .= '<div class="form-group">';
					$html .= '<label class="small">'.strtoupper($field->name).'</label>';
					$field_type = strtolower($field->type);
					switch ($field_type) {
						case 'int':
							$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'double':
							$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'varchar':
							$html .= form_input(['type'=>'text','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'text':
							$html .= form_textarea(['class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'date':
							$html .= form_input(['type'=>'text','class'=>'form-control input-sm datepicker','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						default:
							$html .= '';
							break;
					}
					$html .= '</div>';
				}
			} else {
				$html .= '<div class="form-group">';
				$field_type = strtolower($field->type);
				switch ($field_type) {
					case 'int':
						$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'double':
						$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'varchar':
						$html .= form_input(['type'=>'text','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'text':
						$html .= form_textarea(['class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'date':
						$html .= form_input(['type'=>'text','class'=>'form-control input-sm datepicker','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					default:
						$html .= '';
					break;
				}
				$html .= '</div>';
			}
		}
	}
	return $html;
}
function generate_form_group_lg($table,$type,$hidden=[])
{
	$html = '<div class="row">';
	$CI =& get_instance();
	$fields = $CI->db->field_data($table);
	foreach ($fields as $field) {
		if ($field->primary_key) {
			if ($type != 'insert' || $type != 'tambah') {
				$html .= '<input type="hidden" id="'.$type.'_'.$field->name.'" name="'.$field->name.'">';
			}
		} else {
			if (count($hidden)>0) {
				if (in_array($field->name,$hidden) != false) {
					$html .= form_input(['type'=>'hidden','id'=>$type.'_'.$field->name,'name'=>$field->name]);
				} else {
					$html .= '<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">';
					$html .= '<label class="small">'.strtoupper($field->name).'</label>';
					$field_type = strtolower($field->type);
					switch ($field_type) {
						case 'int':
							$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'double':
							$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'varchar':
							$html .= form_input(['type'=>'text','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'text':
							$html .= form_textarea(['class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						case 'date':
							$html .= form_input(['type'=>'text','class'=>'form-control input-sm datepicker','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
							break;
						default:
							$html .= '';
							break;
					}
					$html .= '</div>';
				}
			} else {
				$html .= '<div class="form-group">';
				$field_type = strtolower($field->type);
				switch ($field_type) {
					case 'int':
						$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'double':
						$html .= form_input(['type'=>'number','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'varchar':
						$html .= form_input(['type'=>'text','class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'text':
						$html .= form_textarea(['class'=>'form-control input-sm','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					case 'date':
						$html .= form_input(['type'=>'text','class'=>'form-control input-sm datepicker','id'=>$type.'_'.$field->name,'name'=>$field->name,'placeholder'=>ucfirst($field->name),'required'=>'true']);
					break;
					default:
						$html .= '';
					break;
				}
				$html .= '</div>';
			}
		}
	}
	$html .= '</div>';
	return $html;
}