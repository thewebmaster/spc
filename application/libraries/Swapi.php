<?php
use \Curl\Curl;

class Swapi {
    const STATUS_CODE_OK = 200;
    const STATUS_INTERNAL_SERVER_ERROR = 500;
    const BASE_URL = 'https://swapi.co/api/';
    const RETURN_OF_JEDI = 'Return of the Jedi';

    const RESOURCE_SPECIES = 'species';
    const RESOURCE_PLANETS = 'planets';
    const RESOURCE_FILMS = 'films';
    const RESOURCE_PEOPLE = 'people';

    private $curl;

    /**
     * Method to do get request to Swapi
     * @param string $url API URL to do get request
     * @return array Array of response data ['code','error','data']
     */
    private function request($url)
    {
        $this->curl->get($url);
        if ($this->curl->error) {
            log_message('debug', 'Error response received from SWAPI API: '. print_r([
                'url' => $url,
                'code' => $this->curl->errorCode,
                'message' => $this->curl->errorMessage,
            ], true));
            return [
                'code' => $this->curl->errorCode,
                'error' => 'Error: ' . $this->curl->errorCode . ': ' . $this->curl->errorMessage,
                'data' => null
            ];
        } else {
            log_message('debug', 'Successful response received from SWAPI API: '. print_r([
                'url' => $url,
                'code' => $this->curl->getHttpStatusCode(),
                'data' => $this->curl->response
            ], true));
            return [
                'code' => $this->curl->getHttpStatusCode(),
                'error' => '',
                'data' => $this->curl->response
            ];
        }
    }

    /**
     * Get all resource data from Swapi
     * @param string $resource Swapi resource name
     * @param string $key (optional) Specify which resource field to be used as array key. Default: name
     * @return array Array of resource data ['code','error','data']
     */
    private function get_all_resource($resource, $key = 'name')
    {
        $resource_list = [];
        $next = self::BASE_URL . sprintf('%s/', $resource);
        do {
            $resource_data = $this->request($next);
            if ($resource_data['code'] === self::STATUS_CODE_OK && !empty($resource_data['data'])) {
                $results = $resource_data['data']->results;
                $next = $resource_data['data']->next;
                foreach($results as $result) {
                    $resource_list[$result->{$key}] = $result;
                }
            }
        } while($next !== null && $resource_data['code'] === self::STATUS_CODE_OK);
        return [
            'code' => $resource_data['code'],
            'error' => $resource_data['error'],
            'data' => $resource_list
        ];
    }

    public function __construct()
    {
        $this->curl = new Curl();
    }

    /**
     * Get a list of all characters from the file 'Return Of The Jedi'
     * @return array Array of characters data ['code','error','data']
     */
    public function get_jedi_characters()
    {
        $characters = [];
        $films = $this->search_resources(self::RESOURCE_FILMS, self::RETURN_OF_JEDI);
        if ($films['code'] === self::STATUS_CODE_OK && !empty($films['data']) && count($films['data']) === 1) {
            $characters = $films['data'][0]->characters;
        }
        return [
            'code' => $films['code'],
            'error' => $films['error'],
            'data' => $characters
        ];
    }

    /**
     * Get a list of all species in all Star Wars films
     * @return array Array of species data ['code','error','data']
     */
    public function get_all_star_wars_species()
    {
        $species = [];
        $films = $this->request(self::BASE_URL . sprintf('%s/', self::RESOURCE_FILMS));
        if ($films['code'] === self::STATUS_CODE_OK && !empty($films['data'])) {
            foreach($films['data']->results as $film) {
                $species = array_merge_recursive($species, $film->species);
            }
        }
        return [
            'code' => $films['code'],
            'error' => $films['error'],
            'data' => array_unique($species)
        ];
    }

    /**
     * Get all people from Swapi
     * @return array Array of people data ['code','error','data']
     */
    public function get_all_people()
    {
        // Get list of all homeworlds
        $planets = $this->get_all_resource(self::RESOURCE_PLANETS, 'url');
        if ($planets['code'] !== self::STATUS_CODE_OK) {
            return [
                'code' => $planets['code'],
                'error' => $planets['error'],
                'data' => []
            ];
        }

        // Get list of all species
        $species = $this->get_all_resource(self::RESOURCE_SPECIES, 'url');
        if ($species['code'] !== self::STATUS_CODE_OK) {
            return [
                'code' => $species['code'],
                'error' => $species['error'],
                'data' => []
            ];
        }
        
        $people = [];
        $next = self::BASE_URL . sprintf('%s/', self::RESOURCE_PEOPLE);
        do {
            $people_data = $this->request($next);
            if ($people_data['code'] === self::STATUS_CODE_OK && !empty($people_data['data'])) {
                $results = $people_data['data']->results;
                $next = $people_data['data']->next;
                foreach($results as $result) {
                    $people[$result->name] = [
                        'name' => $result->name,
                        'height' => $result->height,
                        'mass' => $result->mass,
                        'hair_color' => $result->hair_color,
                        'birth_year' => $result->birth_year,
                        'gender' => $result->gender,
                        'homeworld_name' => $planets['data'][$result->homeworld]->name,
                        'species_name' => (isset($result->species[0])) ? $species['data'][$result->species[0]]->name : null,
                    ];
                }
            }
        } while($next !== null);
        return [
            'code' => $people_data['code'],
            'error' => $people_data['error'],
            'data' => $people
        ];
    }

    /**
     * Prepare and verify update records
     * @param array $update_records Array of records collected from `update_character_data` table
     */
    public function prepare_update_records($update_records)
    {
        // Get list of all homeworlds
        $planets = $this->get_all_resource(self::RESOURCE_PLANETS);
        if ($planets['code'] !== self::STATUS_CODE_OK) {
            return [
                'code' => $planets['code'],
                'error' => $planets['error'],
                'data' => []
            ];
        }

        // Get list of all species
        $species = $this->get_all_resource(self::RESOURCE_SPECIES);
        if ($species['code'] !== self::STATUS_CODE_OK) {
            return [
                'code' => $species['code'],
                'error' => $species['error'],
                'data' => []
            ];
        }

        $sanitized_update_records = [];
        foreach($update_records as $update_record) {
            $sanitized_update_records[$update_record['name']] = [
                'name' => $update_record['name'],
                'height' => $update_record['height'],
                'mass' => $update_record['mass'],
                'hair_color' => $update_record['hair_color'],
                'birth_year' => $update_record['birth_year'],
                'gender' => $update_record['gender'],
            ];

            // Ensure that if updating ‘home world’ that the value exists in SWAPI API
            if (isset($planets['data'][$update_record['homeworld_name']])) {
                $sanitized_update_records[$update_record['name']]['homeworld_name'] = $update_record['homeworld_name'];
            }

            // Ensure that if updating ‘species’ that the value exists in SWAPI API
            if (isset($species['data'][$update_record['species_name']])) {
                $sanitized_update_records[$update_record['name']]['species_name'] = $update_record['species_name'];
            }
        }

        return [
            'code' => self::STATUS_CODE_OK,
            'error' => '',
            'data' => $sanitized_update_records
        ];
    }

    /**
     * Get resource(s) by the given search keyword
     * @return array Array of resource data ['code','error','data']
     */
    public function search_resources($resource, $keyword)
    {
        $result = [];
        $films = $this->request(self::BASE_URL . sprintf('%s/?search=%s',$resource, urlencode($keyword)));
        if ($films['code'] === self::STATUS_CODE_OK && !empty($films['data'])) {
            $result = $films['data']->results;
        }
        return [
            'code' => $films['code'],
            'error' => $films['error'],
            'data' => $result
        ];
    }
}