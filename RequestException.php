<?php

namespace SamYapp\VacancyAPIClient;

/**
 * Represents an error as returned by an invalid request to the vacancies api
 */
class RequestException extends \Exception
{
    /**
     * Create a RequestException from the provided XML
     * @param $xml
     * @return RequestException
     */
	public static function fromXML( $xml )
	{
		return new RequestException( $xml->errorcode, $xml->errordescription );
	}

    public function __construct( $code, $description )
    {
        parent::__construct( 'RequestException - Code: ' . $code . ' - ' . $description );
    }
}
