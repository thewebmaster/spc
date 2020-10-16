<?php
class Character_model extends CI_Model {
    const TABLE_NAME = 'swapi_characters';
    const UPDATE_TABLE_NAME = 'update_character_data';

    /**
     * Insert records to swapi_characters table in batches
     * @param array $data Swapi character records to be inserted
     * @return mixed number of rows inserted or boolean false if failed to insert records
     */
    public function insert_batch($data)
    {
        return $this->db->insert_batch(self::TABLE_NAME, $data, true);
    }
    
    /**
     * Truncates swapi_characters table
     */
    public function truncate()
    {
        $this->db->truncate(self::TABLE_NAME);
    }

    /**
     * Updates records from swapi_characters table in batches
     * @param array $data The associative array of Swapi character records
     * @param string $index The `where` key
     * @return int Number of rows affected
     */
    public function update_batch($data, $index)
    {
        return $this->db->update_batch(self::TABLE_NAME, $data, $index);
    }

    /**
     * Get all records from `update_character_data`
     * @return array Array of records from `update_character_data` table
     */
    public function get_all_from_update_table()
    {
        $this->db->from(self::UPDATE_TABLE_NAME);
        $query = $this->db->get();
        return $query ? $query->result_array() : [];
    }

    /**
     * Inserts a record to `swapi_characters`
     * @return void
     */
    public function insert($data)
    {
        $this->db->set($data);
        $this->db->insert(self::TABLE_NAME);
    }
}
