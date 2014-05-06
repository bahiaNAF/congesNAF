<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demande
 *
 * @ORM\Table(name="demande")
 * @ORM\Entity
 */
class Demande
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_dem", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idDem;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_d", type="date", nullable=false)
     */
    private $dateD;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_f", type="date", nullable=false)
     */
    private $dateF;

    /**
     * @var string
     *
     * @ORM\Column(name="nbr_jr", type="string", length=255, nullable=false)
     */
    private $nbrJr;

    /**
     * @var string
     *
     * @ORM\Column(name="msg", type="text", nullable=false)
     */
    private $msg;

    /**
     * @var integer
     *
     * @ORM\Column(name="de_midi", type="integer", nullable=false)
     */
    private $deMidi;

    /**
     * @var integer
     *
     * @ORM\Column(name="jsq_midi", type="integer", nullable=false)
     */
    private $jsqMidi;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cng", type="integer", nullable=false)
     */
    private $idCng;

    /**
     * @var string
     *
     * @ORM\Column(name="id_etat", type="string", length=20, nullable=false)
     */
    private $idEtat = 'a';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;

    /**
     * @var string
     *
     * @ORM\Column(name="validateur", type="string", length=255, nullable=false)
     */
    private $validateur = 'vide';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_val", type="date", nullable=false)
     */
    private $dateVal;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_enrg", type="date", nullable=false)
     */
    private $dateEnrg;



    /**
     * Get idDem
     *
     * @return integer 
     */
    public function getIdDem()
    {
        return $this->idDem;
    }

    /**
     * Set dateD
     *
     * @param \DateTime $dateD
     * @return Demande
     */
    public function setDateD($dateD)
    {
        $this->dateD = $dateD;

        return $this;
    }

    /**
     * Get dateD
     *
     * @return \DateTime 
     */
    public function getDateD()
    {
        return $this->dateD;
    }

    /**
     * Set dateF
     *
     * @param \DateTime $dateF
     * @return Demande
     */
    public function setDateF($dateF)
    {
        $this->dateF = $dateF;

        return $this;
    }

    /**
     * Get dateF
     *
     * @return \DateTime 
     */
    public function getDateF()
    {
        return $this->dateF;
    }

    /**
     * Set nbrJr
     *
     * @param string $nbrJr
     * @return Demande
     */
    public function setNbrJr($nbrJr)
    {
        $this->nbrJr = $nbrJr;

        return $this;
    }

    /**
     * Get nbrJr
     *
     * @return string 
     */
    public function getNbrJr()
    {
        return $this->nbrJr;
    }

    /**
     * Set msg
     *
     * @param string $msg
     * @return Demande
     */
    public function setMsg($msg)
    {
        $this->msg = $msg;

        return $this;
    }

    /**
     * Get msg
     *
     * @return string 
     */
    public function getMsg()
    {
        return $this->msg;
    }

    /**
     * Set deMidi
     *
     * @param integer $deMidi
     * @return Demande
     */
    public function setDeMidi($deMidi)
    {
        $this->deMidi = $deMidi;

        return $this;
    }

    /**
     * Get deMidi
     *
     * @return integer 
     */
    public function getDeMidi()
    {
        return $this->deMidi;
    }

    /**
     * Set jsqMidi
     *
     * @param integer $jsqMidi
     * @return Demande
     */
    public function setJsqMidi($jsqMidi)
    {
        $this->jsqMidi = $jsqMidi;

        return $this;
    }

    /**
     * Get jsqMidi
     *
     * @return integer 
     */
    public function getJsqMidi()
    {
        return $this->jsqMidi;
    }

    /**
     * Set idCng
     *
     * @param integer $idCng
     * @return Demande
     */
    public function setIdCng($idCng)
    {
        $this->idCng = $idCng;

        return $this;
    }

    /**
     * Get idCng
     *
     * @return integer 
     */
    public function getIdCng()
    {
        return $this->idCng;
    }

    /**
     * Set idEtat
     *
     * @param string $idEtat
     * @return Demande
     */
    public function setIdEtat($idEtat)
    {
        $this->idEtat = $idEtat;

        return $this;
    }

    /**
     * Get idEtat
     *
     * @return string 
     */
    public function getIdEtat()
    {
        return $this->idEtat;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return Demande
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer 
     */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
     * Set validateur
     *
     * @param string $validateur
     * @return Demande
     */
    public function setValidateur($validateur)
    {
        $this->validateur = $validateur;

        return $this;
    }

    /**
     * Get validateur
     *
     * @return string 
     */
    public function getValidateur()
    {
        return $this->validateur;
    }

    /**
     * Set dateVal
     *
     * @param \DateTime $dateVal
     * @return Demande
     */
    public function setDateVal($dateVal)
    {
        $this->dateVal = $dateVal;

        return $this;
    }

    /**
     * Get dateVal
     *
     * @return \DateTime 
     */
    public function getDateVal()
    {
        return $this->dateVal;
    }

    /**
     * Set dateEnrg
     *
     * @param \DateTime $dateEnrg
     * @return Demande
     */
    public function setDateEnrg($dateEnrg)
    {
        $this->dateEnrg = $dateEnrg;

        return $this;
    }

    /**
     * Get dateEnrg
     *
     * @return \DateTime 
     */
    public function getDateEnrg()
    {
        return $this->dateEnrg;
    }
}
