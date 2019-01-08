<?php

namespace FormArmorBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sessions_Autorisees
 *
 * @ORM\Table(name="sessions_autorisees")
 * @ORM\Entity(repositoryClass="FormArmorBundle\Repository\Sessions_AutoriseesRepository")
 */
class Sessions_Autorisees
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
	* @ORM\ManyToOne (targetEntity="FormArmorBundle\Entity\Session_formation")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $session;
    
    /**
	* @ORM\ManyToOne (targetEntity="FormArmorBundle\Entity\Formation")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $formation;

    /**
	* @ORM\ManyToOne (targetEntity="FormArmorBundle\Entity\Client")
	* @ORM\JoinColumn(nullable=false)
	*/
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="niveau", type="string", length=255)
     */
    private $niveau;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", length=255)
     */
    private $date;

    /**
     * @var int
     *
     * @ORM\Column(name="nbPlaces", type="integer")
     */
    private $nbPlaces;

    /**
     * @var int
     *
     * @ORM\Column(name="nbInscrits", type="integer")
     */
    private $nbInscrits;

    /**
     * @var bool
     *
     * @ORM\Column(name="ferme", type="boolean")
     */
    private $ferme;


    /**
    * Set formation
    *
    * @param \FormArmorBundle\Entity\Formation $formation
    *
    * @return Session_formation
    */
    public function setFormation(\FormArmorBundle\Entity\Formation $formation)
    {
        $this->formation = $formation;

        return $this;
    }

    /**
    * Get formation
    *
    * @return \FormArmorBundle\Entity\Formation
    */
    public function getFormation()
    {
        return $this->formation;
    }

     /**
    * Set client
    *
    * @param \FormArmorBundle\Entity\Client $client
    *
    * @return client
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
    * Set session
    *
    * @param \FormArmorBundle\Entity\Session_formation $session
    *
    * @return Session_formation
    */
    public function setSession(\FormArmorBundle\Entity\Session_formation $session)
    {
        $this->session = $session;

        return $this;
    }

    /**
    * Get session
    *
    * @return \FormArmorBundle\Entity\Session_formation
    */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set libelle
     *
     * @param string $libelle
     *
     * @return SessionsAutorisees
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set niveau
     *
     * @param string $niveau
     *
     * @return SessionsAutorisees
     */
    public function setNiveau($niveau)
    {
        $this->niveau = $niveau;

        return $this;
    }

    /**
     * Get niveau
     *
     * @return string
     */
    public function getNiveau()
    {
        return $this->niveau;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return SessionsAutorisees
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return SessionsAutorisees
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set nbPlaces
     *
     * @param integer $nbPlaces
     *
     * @return SessionsAutorisees
     */
    public function setNbPlaces($nbPlaces)
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    /**
     * Get nbPlaces
     *
     * @return int
     */
    public function getNbPlaces()
    {
        return $this->nbPlaces;
    }

    /**
     * Set nbInscrits
     *
     * @param integer $nbInscrits
     *
     * @return SessionsAutorisees
     */
    public function setNbInscrits($nbInscrits)
    {
        $this->nbInscrits = $nbInscrits;

        return $this;
    }

    /**
     * Get nbInscrits
     *
     * @return int
     */
    public function getNbInscrits()
    {
        return $this->nbInscrits;
    }

    /**
     * Set ferme
     *
     * @param boolean $ferme
     *
     * @return SessionsAutorisees
     */
    public function setFerme($ferme)
    {
        $this->ferme = $ferme;

        return $this;
    }

    /**
     * Get ferme
     *
     * @return bool
     */
    public function getFerme()
    {
        return $this->ferme;
    }
    
}

