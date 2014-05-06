<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CompteurSolde
 *
 * @ORM\Table(name="compteur_solde")
 * @ORM\Entity
 */
class CompteurSolde
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ref_cpt", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $refCpt;

    /**
     * @var string
     *
     * @ORM\Column(name="annee", type="string", length=100, nullable=false)
     */
    private $annee;

    /**
     * @var float
     *
     * @ORM\Column(name="cpt_initial", type="float", precision=10, scale=0, nullable=false)
     */
    private $cptInitial;

    /**
     * @var float
     *
     * @ORM\Column(name="cpt_solde", type="float", precision=10, scale=0, nullable=false)
     */
    private $cptSolde;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_user", type="integer", nullable=false)
     */
    private $idUser;



    /**
     * Get refCpt
     *
     * @return integer 
     */
    public function getRefCpt()
    {
        return $this->refCpt;
    }

    /**
     * Set annee
     *
     * @param string $annee
     * @return CompteurSolde
     */
    public function setAnnee($annee)
    {
        $this->annee = $annee;

        return $this;
    }

    /**
     * Get annee
     *
     * @return string 
     */
    public function getAnnee()
    {
        return $this->annee;
    }

    /**
     * Set cptInitial
     *
     * @param float $cptInitial
     * @return CompteurSolde
     */
    public function setCptInitial($cptInitial)
    {
        $this->cptInitial = $cptInitial;

        return $this;
    }

    /**
     * Get cptInitial
     *
     * @return float 
     */
    public function getCptInitial()
    {
        return $this->cptInitial;
    }

    /**
     * Set cptSolde
     *
     * @param float $cptSolde
     * @return CompteurSolde
     */
    public function setCptSolde($cptSolde)
    {
        $this->cptSolde = $cptSolde;

        return $this;
    }

    /**
     * Get cptSolde
     *
     * @return float 
     */
    public function getCptSolde()
    {
        return $this->cptSolde;
    }

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return CompteurSolde
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
}
