<?php

namespace FormArmorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Inscription
 *
 * @ORM\Table(name="inscription")
 * @ORM\Entity(repositoryClass="FormArmorBundle\Repository\InscriptionRepository")
 */
class Inscription
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

	 /**
	 * @ORM\ManyToOne (targetEntity="FormArmorBundle\Entity\Client")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $client;
	
	/**
	 * @ORM\ManyToOne (targetEntity="FormArmorBundle\Entity\Session_formation")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $sessionFormation;
	
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_inscription", type="date")
     */
    private $dateInscription;

     /**
     * @var int
     *
     * @ORM\Column(name="presence", type="boolean")
     */
    private $presence;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateInscription
     *
     * @param \DateTime $dateInscription
     *
     * @return Inscription
     */
    public function setDateInscription($dateInscription)
    {
        $this->dateInscription = $dateInscription;

        return $this;
    }

    /**
     * Get dateInscription
     *
     * @return \DateTime
     */
    public function getDateInscription()
    {
        return $this->dateInscription;
    }

    /**
     * Set client
     *
     * @param \FormArmorBundle\Entity\Client $client
     *
     * @return Inscription
     */
    public function setClient(\FormArmorBundle\Entity\Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \FormArmorBundle\Entity\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set sessionFormation
     *
     * @param \FormArmorBundle\Entity\Session_formation $sessionFormation
     *
     * @return Inscription
     */
    public function setSessionFormation(\FormArmorBundle\Entity\Session_formation $sessionFormation)
    {
        $this->sessionFormation = $sessionFormation;

        return $this;
    }

    /**
     * Get sessionFormation
     *
     * @return \FormArmorBundle\Entity\Session_formation
     */
    public function getSessionFormation()
    {
        return $this->sessionFormation;
    }

    /**
     * Get presence
     *
     * @return int
     */
    public function getPresence()
    {
        return $this->presence;
    }

    /**
     * Set presence
     *
     * @param integer $presence
     * 
     * @return Inscription
     */
    public function setPresence($presence)
    {
        $this->presence = $presence;

        return $this;
    }
}
