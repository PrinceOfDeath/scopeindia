<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

  class Patient extends CI_Controller {
    public function index(){
      if(!$this->session->userdata('logged_in')){
        redirect('');
      } else {
        //get patient data
        $data['patients'] = $this->patient_model->getlists();
       // print_r($data['patients']);

        //display patients
        $this->load->view('templates/header');
        $this->load->view('patient/index',$data);
        $this->load->view('templates/footer');
      }
    }
    public function add(){
    		if(!$this->session->userdata('logged_in')){
            redirect('');
          } else {
    		//set form rules
    		$this->form_validation->set_rules('pname','Patient Name','required');
    		$this->form_validation->set_rules('paddress','Patient Address','required');
    		$this->form_validation->set_rules('page','Patient Age','required');
    		$this->form_validation->set_rules('pgender','Gender','required');
        $this->form_validation->set_rules('history','Medical History','required');
        $this->form_validation->set_rules('doctor_id','Doctor ID','required');
        $this->form_validation->set_rules('medicine_id','Medicine ID','required');
        $this->form_validation->set_rules('test_id','Test ID','required');
        //get doctors list
        $data['doctors'] = $this->patient_model->getdoctors();
        //get medicine list
        $data['medicines'] = $this->patient_model->getmedicines();
        //get test list
        $data['tests'] = $this->patient_model->gettests();
    		//if not submitted then show the page
    		if($this->form_validation->run() == FALSE){
    		$this->load->view('templates/header');
    		$this->load->view('patient/add',$data);
    		$this->load->view('templates/footer');
    		} else {
    		  $medicine_id= $this->input->post('medicine_id');
              $medi='';
            //$medi.="{";
              for($i=0;$i<count($medicine_id);$i++)
               {
            if($i!=0){ 
             $medi.= ',';
            }
            $medi.= $medicine_id[$i];           
           }
    		  //$medi.="}"; 
            // print_r( $medi);
    			//send in the data to model for insert
    			$data = array(
    				'pname' 			=> $this->input->post('pname'),
    				'paddress' 		=> $this->input->post('paddress'),
    				'page'        => $this->input->post('page'),
    				'pgender' 	  => $this->input->post('pgender'),
    				'history' 	  => $this->input->post('history'),
            'doctor_id' 	=> $this->input->post('doctor_id'),
            'medicine_id' => $medi,
            'test_id' 	  => $this->input->post('test_id')
    			);
    			$add = $this->patient_model->add($data);
    			if($add == FALSE){
            $this->session->set_flashdata('padd_fail','Failed to Add New Patient');
    				redirect('index.php/patient/index');
    			}else {
            $this->session->set_flashdata('padd_success','Successfully Added New Patient');
    				redirect('index.php/patient/index');
    			}
    		}
      }
    }
	public function view($slug){
		if(!$this->session->userdata('logged_in')){
        redirect('');
      } else { 	
	  
	  //get specific patient data
		$data['details'] = $this->patient_model->view($slug);
		$med_id = $data['details']->medicine_id;
		//get doctor details
		$doc_id = $data['details']->doctor_id;
		$data['doctor'] = $this->patient_model->getdoctor($doc_id);
		//get test details
		$test_id = $data['details']->test_id;
		$data['test'] = $this->patient_model->gettest($test_id);
		$data['med_arr'] = explode(",",$med_id);
		$data['medicines'] = $this->patient_model->getmedicines();
		//colorful buttons
		$data['btn'] = array(
			'btn-primary',
			'btn-success',
			'btn-info',
			'btn-warning',
			'btn-danger',
			'btn-link'
		);
		// show the patient data
		$this->load->view('templates/header');
		$this->load->view('patient/view',$data);
		$this->load->view('templates/footer');
	  }
	}
	public function edit($slug){
		if(!$this->session->userdata('logged_in')){
        redirect('');
      } else { 	
		//set form rules 
		$this->form_validation->set_rules('pname','Patient Name','required');
		$this->form_validation->set_rules('paddress','Address','required');
		$this->form_validation->set_rules('page','Age','required');
		$this->form_validation->set_rules('pgender','Gender','required');
		$this->form_validation->set_rules('history','Medical History','required');
		$this->form_validation->set_rules('doctor_id','Doctor ID','required');
		$this->form_validation->set_rules('medicine_id','Medicine ID','required');
		$this->form_validation->set_rules('test_id','Test ID','required');
		//if not submitted then show the page 
			if($this->form_validation->run() == FALSE){
			  //get specific doctor data
				$data['details'] = $this->patient_model->view($slug);
				// show the doctor data
				$this->load->view('templates/header');
				$this->load->view('patient/edit',$data);
				$this->load->view('templates/footer');
			} else {
			//get new userdata
			$data = array(
				'pname' 			=> $this->input->post('pname'),
				'paddress' 		=> $this->input->post('paddress'),
				'page' => $this->input->post('page'),
				'pgender' 	=> $this->input->post('pgender'),
				'history' 	=>$this->input->post('history'),
				'doctor_id' 	=> $this->input->post('doctor_id'),
				'medicine_id' 	=> $this->input->post('medicine_id'),
				'test_id' 	=> $this->input->post('test_id')
			);
			$update = $this->patient_model->update($slug,$data);
			if($update){
				redirect('index.php/patient/index');
			} else {
				redirect('index.php/patient/index');
			}
		  }
		}
	  }
	  //delete doctor 
	  public function delete($slug){
		  $delete = $this->patient_model->delete($slug);
		  redirect('index.php/patient/index');
	  }
  }
