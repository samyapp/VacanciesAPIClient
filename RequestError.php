<?php

namespace SamYapp\VacancyAPI;

/**
 * Represents an error as returned by an invalid request to the vacancies api
 */
class RequestError
{
	public static function fromXML( $xml )
	{
		return new RequestError;
	}
}
