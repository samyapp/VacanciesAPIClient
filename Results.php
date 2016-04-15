<?php

namespace SamYapp\VacancyAPIClient;

/**
 * Contains the result of a successful request to the API.
 */
class Results
{
	public $NoOfVacancies = 0;
	public $JobFrame;
	public $RequestSuccess;
	public $Vacancies = [];

    public static function noResults()
    {
        $results = new Results;
        $results->NoOfVacancies = 0;
        $results->RequestSuccess = 'Fail';
        return $results;
    }

	public static function fromXML( \SimpleXMLElement $xml )
	{
		if ( !isset( $xml->NumberOfVacancies ) || !isset( $xml->RequestSuccess ) ) {
				throw new InvalidArgumentException( 'Invalid XML Data' );
		}
		$results = new Results();
		$results->NoOfVacancies = (string)$xml->NumberOfVacancies;
		$results->JobFrame = (string)$xml->JobFrame;
		$results->RequestSuccess = (string)$xml->RequestSuccess;
		foreach ( $xml->Vacancy as $vacancy ) {
			$results->Vacancies[] = Vacancy::fromSimpleXML( $vacancy );
		}
		return $results;
	}
}