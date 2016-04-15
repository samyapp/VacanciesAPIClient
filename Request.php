<?php

namespace SamYapp\VacancyAPIClient;

/**
 * Make a request to the job vacancies API
 */
class Request
{
	const SORT_JOBID = 'jobid';
	const SORT_ROLE = 'Role';
	const SORT_EXPIRY = 'Expiry';
	const SORT_LOCATION = 'location';
    const SORT_FULLTIME = 'fulltime';
    const SORT_PARTTIME = 'parttime';
    const SORT_JOBTITLE = 'jobtitle';

    // allowed sort bys
    static $AUTO_SORT_FIELDS = [
        self::SORT_JOBID,
	    self::SORT_ROLE,
        self::SORT_EXPIRY,
        self::SORT_LOCATION,
        self::SORT_FULLTIME,
        self::SORT_PARTTIME,
        self::SORT_JOBTITLE
    ];

	const SORTORDER_ASCENDING = '1';
	const SORTORDER_DESCENDING = '0';

    const JOBSTATUS_OPEN = 'Open';
    const JOBSTATUS_LIVE = 'Live';

    const SECURITY_PUBLIC = 'Public';
	const SECURITY_PRIVATE = 'Private';

    const ERROR_NO_LIVE_JOBS = '1003';

	const NO_LIMIT = 0;
	
	public $public_key = '';
	public $api_url = '';
	public $company_id = '';
	public $job_frames = '';
	public $limit = 3;
	public $job_security = '';
	public $sort_field = null;//self::SORT_ROLE;
	public $sort_order = null;//self::SORTORDER_ASCENDING;
    public $job_status = null;

    public $raw_data = null;
    public $xml_data = null;
    public $request_xml = null;

	public function request()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->createRequestXML() );
		$data = curl_exec($ch);
        if ( ! $data ) {
            throw new \HttpException('No response');
        }
        $this->raw_data = $data;
		$data = $this->parseData( $data );
        $this->xml_data = $data;
		return $data;
	}
	
	public function __construct( $company_id, $public_key, $api_url = null )
	{
        $this->company_id = $company_id;
        $this->public_key = $public_key;
        $this->api_url = $api_url ? $api_url : $this->api_url;
	}

    public function getAllJobs()
    {
        return $this->getJobs( 0 );
    }

    public function getJobs( $limit = 0 )
    {
        $this->limit = $limit;
        return $this->request();
    }

    public function from( $frames )
    {
        if ( ! is_array( $frames ) ) {
            $frames = [ $frames ];
        }
        $frames = join(',', $frames );
        $this->job_frames = $frames;
        return $this;
    }

    public function open()
    {
        $this->job_status = self::JOBSTATUS_OPEN;
        return $this;
    }

    public function live()
    {
        $this->job_status = self::JOBSTATUS_LIVE;
        return $this;
    }

    public function publicJobs()
    {
        $this->job_security = self::SECURITY_PUBLIC;
        return $this;
    }

    public function privateJobs()
    {
        $this->job_security = self::SECURITY_PRIVATE;
        return $this;
    }

    public function ascending()
    {
        $this->sort_order = self::SORTORDER_ASCENDING;
        return $this;
    }

    public function descending()
    {
        $this->sort_order = self::SORTORDER_DESCENDING;
        return $this;
    }

    /**
     * Sort by any fo the sort fields.
     * @param $func
     * @param $args
     */
	public function __call( $func, $args )
	{
        print_r( self::$AUTO_SORT_FIELDS );
        print_r( static::$AUTO_SORT_FIELDS );
        if ( preg_match( '/^sortBy(?P<sortfield>[A-Za-z0-9_]+)$/i', $func, $matches ) ) {
            $sortfield= strtolower( $matches['sortfield']);
            foreach ( static::$AUTO_SORT_FIELDS as $field ) {
                if ( strtolower($field) === $sortfield ) {
                    $this->sort_field = $field;
                    return $this;
                }
            }
        }
        throw new \BadMethodCallException( 'Method ' . $func . ' does not exist in ' . __CLASS__ . ' in ' . __FILE__ );
	}
	
	/**
	 * Deal with invalid character encoding, 
	 * simplexml error "input is not proper utf-8 indicate encoding"
	 * from http://stackoverflow.com/a/2510231	
	 */
	private function cleanData($str)
	{
		return preg_replace_callback('#[\\xA1-\\xFF](?![\\x80-\\xBF]{2,})#', function( $m ) { return utf8_encode( $m[0] ); }, $str);
	}

	public function parseData( $data )
    {
		$data = $this->cleanData( $data );
		$xml = simplexml_load_string( $data, "SimpleXMLElement" ,  LIBXML_NOCDATA );
		if ( isset( $xml->RequestSuccess ) && $xml->RequestSuccess == 'Success' ) {
			return Results::fromXML( $xml );
		}
		else if ( isset( $xml->requestsuccess ) && $xml->requestsuccess == 'Fail' ) {
            if ( $xml->errorcode == self::ERROR_NO_LIVE_JOBS ) { // this shouldn't really be an error, should it?
                return Results::noResults();
            }
            else {
                throw RequestException::fromXML($xml);
            }
		}
        else {
            throw new \Exception( 'Invalid response from API: ' . $data );
        }
	}
	
	private function createRequestXML()
	{
		$this->request_xml = "<InterfaceRequest>
<RequestMethod>GetVacancies</RequestMethod>
<Request>
<PublicKey>{$this->public_key}</PublicKey>
<CompanyID>{$this->company_id}</CompanyID>";
        if ( $this->sort_field ) {
            $this->request_xml .= "<SortField>{$this->sort_field}</SortField>";
        }
        if ( $this->sort_order ) {
            $this->request_xml .= "<SortOrder>{$this->sort_order}</SortOrder>";
        }
        if ( $this->job_frames ) {
            $this->request_xml .= "<JobFrame>{$this->job_frames}</JobFrame>";
        }
        if ( $this->job_security ) {
            $this->request_xml .= "<JobSecurity>{$this->job_security}</JobSecurity>";
        }
        if ( $this->limit ) {
            $this->request_xml .= "<Limit>{$this->limit}</Limit>";
        }
        if ( $this->job_status ) {
            $this->request_xml .= "<JobStatus>{$this->job_status}</JobStatus>";
        }
        $this->request_xml .= "
</Request>
</InterfaceRequest>";
        return $this->request_xml;
	}
}

