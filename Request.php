<?php

namespace SamYapp\VacanciesAPI;

/**
 * Make a request to the job vacancies API
 */
class Request
{
	// 
	const SORT_JOBID = 'jobid';
	const SORT_ROLE = 'role';
	const SORT_EXPIRY = 'expiry';
	const SORT_LOCATION = 'location';
	const SORT_FULLTIME = 'fulltime/parttime';
	
	const SORTORDER_ASCENDING = 0;
	const SORTORDER_DESCENDING = 1;
	
	const SECURITY_PUBLIC = 'Public';
	const SECURITY_PRIVATE = 'Private';

	const NO_LIMIT = 0;
	
	public $public_key = '';
	public $api_url = '';
	public $company_id = '';
	public $job_frames = '';
	public $limit = 3;
	public $job_security = 'public';
	public $sort_field = self::SORT_ROLE;
	public $sort_order = self::SORTORDER_ASCENDING;
	
	public function request()
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->createRequestXML() );
		$data = curl_exec($ch);
//		$fp = fopen( dirname( __FILE__ ) . '/output.txt', 'w');
//		fwrite( $fp, print_r( $data, true ) );
//		fclose($fp);
		$data = $this->parseData( $data );
		return $data;
	}
	
	public function __construct()
	{
		return $this;
	}
	
	public function __call( $func, $args )
	{
		if ( preg_match( '/^set_(?P<property>[a-z0-9_]+)$/i', $func, $match ) ) {
			$property = $match['property'];
			$this->$property = $args[0];
		}
		return $this;
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
		$result = null;
		if ( isset( $xml->RequestSuccess ) && $xml->RequestSuccess == 'Success' ) {
			return Results::fromXML( $xml );
		}
		else {
			return RequestError::fromXML( $xml );
		}
	}
	
	private function createRequestXML()
	{
		return "<InterfaceRequest>
<RequestMethod>GetVacancies</RequestMethod>
<Request>
<PublicKey>{$this->public_key}</PublicKey>
<CompanyID>{$this->company_id}</CompanyID>
<SortField>{$this->sort_field}</SortField>
<SortOrder>{$this->sort_order}</SortOrder>
<JobFrame>{$this->job_frames}</JobFrame>
<JobSecurity>{$this->job_security}</JobSecurity>
<Limit>{$this->limit}</Limit>
</Request>
</InterfaceRequest>";
	}
}

