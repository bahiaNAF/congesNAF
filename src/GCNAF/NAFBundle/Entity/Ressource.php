<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ressource
 *
 * @ORM\Table(name="ressource")
 * @ORM\Entity
 */
class Ressource
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="cin", type="string", length=255, nullable=false)
     */
    private $cin;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=300, nullable=false)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=300, nullable=false)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="pwd", type="string", length=300, nullable=false)
     */
    private $pwd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_emb", type="date", nullable=false)
     */
    private $dateEmb;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_sor", type="date", nullable=false)
     */
    private $dateSor;

    /**
     * @var string
     *
     * @ORM\Column(name="mail", type="string", length=255, nullable=false)
     */
    private $mail;

    /**
     * @var string
     *
     * @ORM\Column(name="id_prof", type="string", length=20, nullable=false)
     */
    private $idProf;



    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cin
     *
     * @param string $cin
     * @return Ressource
     */
    public function setCin($cin)
    {
        $this->cin = $cin;

        return $this;
    }

    /**
     * Get cin
     *
     * @return string 
     */
    public function getCin()
    {
        return $this->cin;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return Ressource
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return Ressource
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string 
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set login
     *
     * @param string $login
     * @return Ressource
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * Get login
     *
     * @return string 
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * Set pwd
     *
     * @param string $pwd
     * @return Ressource
     */
    public function setPwd($pwd)
    {
        $this->pwd = $pwd;

        return $this;
    }

    /**
     * Get pwd
     *
     * @return string 
     */
    public function getPwd()
    {
        return $this->pwd;
    }

    /**
     * Set dateEmb
     *
     * @param \DateTime $dateEmb
     * @return Ressource
     */
    public function setDateEmb($dateEmb)
    {
        $this->dateEmb = $dateEmb;

        return $this;
    }

    /**
     * Get dateEmb
     *
     * @return \DateTime 
     */
    public function getDateEmb()
    {
        return $this->dateEmb;
    }

    /**
     * Set dateSor
     *
     * @param \DateTime $dateSor
     * @return Ressource
     */
    public function setDateSor($dateSor)
    {
        $this->dateSor = $dateSor;

        return $this;
    }

    /**
     * Get dateSor
     *
     * @return \DateTime 
     */
    public function getDateSor()
    {
        return $this->dateSor;
    }

    /**
     * Set mail
     *
     * @param string $mail
     * @return Ressource
     */
    public function setMail($mail)
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * Get mail
     *
     * @return string 
     */
    public function getMail()
    {
        return $this->mail;
    }

    /**
     * Set idProf
     *
     * @param string $idProf
     * @return Ressource
     */
    public function setIdProf($idProf)
    {
        $this->idProf = $idProf;

        return $this;
    }

    /**
     * Get idProf
     *
     * @return string 
     */
    public function getIdProf()
    {
        return $this->idProf;
    }
}
