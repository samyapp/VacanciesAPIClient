<?php

namespace SamYapp\VacanciesAPI;

/**
 * The details of a job vacancy as returned by the API.
 */
class Vacancy
{
	/**
	 * Unique id for this vacancy.
	 */
	public $JobID;
	
	/**
	 * @var string Designated Advertisement title as specified within the advertisement.
	 */
	public $AdvertTitle;
	
	/**
	 * @var string The designated job title as specified within the advertisement.
	 */
	public $RealJobTitle;
	
	/**
	 * @var string The designated text salary description as specified within the advertisement.
	 */
	public $SalaryDescription;
	
	/**
	 * @var string The type of job
	 * Permanent - Designates a Permanent job type
	 * Temporary - Designates a Temporary job type
	 * Contract - Designates a Contract job type
	 * Traineeship - Designates a Traineeship job type
	 * Apprenticeship - Designates a Apprenticeship job type
	 * Work Placement - Designates a Work Placement job type
	 * Casual - Designates a Casual job type
	 * Volunteer - Designates a Volunteer job type
	 * Practice Job - Designates a Practice Job type
	 */
	public $JobType;
	
	/**
	 * Full-Time - Designates a Full-Time job
	 * Part-Time - Designates a Part-Time job
	 */
	public $JobTime;
	
	/**
	 * @var string A full description of the job as specified within the advertisement and may contain HTML formatting tags.
	 */
	public $AdvertText;
	
	/**
	 * @var string The location of the vacancy as specified in the location text field of the advertisement.
	 */
	public $JobLocation;
	
	/**
	 * @var string The date the advertisement is scheduled to expire and is given in the format 'YYYY-MM-DD HH:MM:SS'.
	 */
	public $ExpiryDate;
	
	/**
	 * @var string The full name of the individual who receives notifications of applications on vacancies as specified within the advertisement.
	 */
	public $ContactName;
	
	/**
	 * @var string The email address that notifications of applications are sent to as specified within the advertisement.
	 */
	public $ContactEmail;
	
	/**
	 * @var string The telephone number given to applicants to ask questions or query regarding the vacancy as specified within the advertisement.
	 */
	public $ContactTelephone;
	
	/**
	 * @var string The name of the company that owns the advertisement as specified within the advertisement.
	 */
	public $AdvertisingCompany;
	
	/**
	 * @var string A URL that redirects the user to an application page for applying to the job directly on the website.
	 */
	public $AdvertLink;
	
	public static function fromSimpleXML( \SimpleXMLElement $el )
	{
		$result = new Vacancy();
		foreach ( [ 'AdvertLink', 'AdvertisingCompany', 'ContactTelephone', 'ContactEmail', 'ContactName',
					'ExpiryDate', 'JobLocation', 'AdvertText', 'JobTime', 'JobType',
					'JobID', 'JobLocation', 'SalaryDescription', 'RealJobTitle', 'AdvertTitle'] as $prop ) {
			$result->$prop = (string)$el->$prop;
		}
		return $result;
	}
}

