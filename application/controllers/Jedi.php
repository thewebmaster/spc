<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jedi extends MY_Controller {
	public function index()
	{
		$this->data['page_title'] = 'Tasks';
		$this->frontend_view('home_page');
	}

	public function characters_jedi()
	{
		$response = $this->swapi->get_jedi_characters();
		if ($response['code'] === Swapi::STATUS_CODE_OK) {
			$this->data['page_title'] = 'Jedi Characters';
			$this->data['characters'] = $response['data'];
			$this->frontend_view('characters_jedi');
		} else {
			show_error($response['error'], $response['code']);
		}
	}

	public function mammal_homeworlds()
	{
		$response = $this->swapi->get_all_star_wars_species();
		if ($response['code'] === Swapi::STATUS_CODE_OK) {
			$this->data['page_title'] = 'Star Wars Mammals';
			$this->data['species'] = $response['data'];
			$this->frontend_view('mammal_homeworlds');
		} else {
			show_error($response['error'], $response['code']);
		}
	}

	public function import_characters()
	{
		$response = $this->swapi->get_all_people();
		if ($response['code'] === Swapi::STATUS_CODE_OK) {
			$this->load->model('character_model');

			// Ensure to have a clean table when import characters page is tested.
			$this->character_model->truncate();

			$result = $this->character_model->insert_batch($response['data']);
			if ($result) {
				$this->data['page_title'] = 'The following characters have been imported successfully';
				$this->data['characters'] = $response['data'];
				$this->frontend_view('import_characters');
			} else {
				show_error('Failed to insert records.', Swapi::STATUS_INTERNAL_SERVER_ERROR);
			}
		} else {
			show_error($response['error'], $response['code']);
		}
	}

	public function update_characters()
	{
		$this->load->model('character_model');
		$update_records = $this->character_model->get_all_from_update_table();
		$response = $this->swapi->prepare_update_records($update_records);
		if ($response['code'] === Swapi::STATUS_CODE_OK) {
			$result = $this->character_model->update_batch($response['data'], 'name');
			if ($result) {
				$this->data['page_title'] = 'The following characters have been updated successfully';
				$this->data['characters'] = $response['data'];
				$this->frontend_view('update_characters');
			} else {
				show_error('Failed to update records.', Swapi::STATUS_INTERNAL_SERVER_ERROR);
			}
		} else {
			show_error($response['error'], $response['code']);
		}
	}

	public function create_character()
	{
		$this->load->helper(['form', 'url']);
		$this->load->library('session');
		if ($this->input->post()) {
			$this->load->library('form_validation');
			$config = [
				['field' => 'name', 'label' => 'Name', 'rules' => 'required'],
				['field' => 'height', 'label' => 'Height', 'rules' => 'required'],
				['field' => 'mass', 'label' => 'Mass', 'rules' => 'required'],
				['field' => 'hair_color', 'label' => 'Hair Colour', 'rules' => 'required'],
				['field' => 'birth_year', 'label' => 'Birth Year', 'rules' => 'required'],
				['field' => 'gender', 'label' => 'Gender', 'rules' => 'required'],
				['field' => 'homeworld_name', 'label' => 'Homeworld Name', 'rules' => 'required|callback_planet_check'],
				['field' => 'species_name', 'label' => 'Species Name', 'rules' => 'callback_species_check'],
			];
			$this->form_validation->set_rules($config);
			if($this->form_validation->run()) {
				$post_data = array_map('strtolower', $this->input->post(null, true));
				$post_data = array_map('ucwords', $post_data);
				unset($post_data['submit']);
				$this->load->model('character_model');
				$this->character_model->insert($post_data);
				$this->session->set_flashdata('character', $post_data);
				redirect('/create-character', 'refresh');
			}
		}

		if($this->session->flashdata('character')) {
			$this->data['page_title'] = 'Character saved successfully';
			$this->data['character'] = $this->session->flashdata('character');
			$this->frontend_view('character_saved');
		} else {
			$this->data['page_title'] = 'Create Character Form';
			$this->frontend_view('create_character');
		}
	}

	/**
	 * FORM VALIDATIONS
	 */
	public function species_check($species)
	{
		if (!empty($species)) {
			$species_data = $this->swapi->search_resources(Swapi::RESOURCE_SPECIES, $species);
			if ($species_data['code'] === Swapi::STATUS_CODE_OK) {
				if (empty($species_data['data'])) {
					$this->form_validation->set_message('species_check', 'The {field} entered does not exists in Swapi API');
					return FALSE;
				}
				else {
					return TRUE;
				}
			} else {
				$this->form_validation->set_message('species_check', 'Failed to verify {field} from Swapi API: ' . $species_data['error']);
				return FALSE;
			}
		}
		return true;
	}
	
	public function planet_check($planet)
	{
		if (!empty($planet)) {
			$planet_data = $this->swapi->search_resources(Swapi::RESOURCE_PLANETS, $planet);
			if ($planet_data['code'] === Swapi::STATUS_CODE_OK) {
				if (empty($planet_data['data'])) {
					$this->form_validation->set_message('planet_check', 'The {field} entered does not exists in Swapi API');
					return FALSE;
				}
				else {
					return TRUE;
				}
			} else {
				$this->form_validation->set_message('planet_check', 'Failed to verify {field} from Swapi API: ' . $planet_data['error']);
				return FALSE;
			}
		}
		return true;
	}
	/**
	 * END FORM VALIDATIONS
	 */
}
