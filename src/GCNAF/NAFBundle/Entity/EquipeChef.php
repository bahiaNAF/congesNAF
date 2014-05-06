<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EquipeChef
 *
 * @ORM\Table(name="equipe_chef")
 * @ORM\Entity
 */
class EquipeChef
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ref", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ref;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_f", type="date")
     */
    private $dateF;

    /**
     * @var integer
     *
     * @ORM\Column(name="projet", type="integer", nullable=false)
     */
    private $projet;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_salarie", type="integer", nullable=false)
     */
    private $idSalarie;

    /**
     * @var string
     *
     * @ORM\Column(name="id_chef", type="string", length=350, nullable=false)
     */
    private $idChef;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_d", type="date", nullable=false)
     */
    private $dateD;



    /**
     * Get ref
     *
     * @return integer 
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * Set dateF
     *
     * @param \DateTime $dateF
     * @return EquipeChef
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
     * Set projet
     *
     * @param integer $projet
     * @return EquipeChef
     */
    public function setProjet($projet)
    {
        $this->projet = $projet;

        return $this;
    }

    /**
     * Get projet
     *
     * @return integer 
     */
    public function getProjet()
    {
        return $this->projet;
    }

    /**
     * Set idSalarie
     *
     * @param integer $idSalarie
     * @return EquipeChef
     */
    public function setIdSalarie($idSalarie)
    {
        $this->idSalarie = $idSalarie;

        return $this;
    }

    /**
     * Get idSalarie
     *
     * @return integer 
     */
    public function getIdSalarie()
    {
        return $this->idSalarie;
    }

    /**
     * Set idChef
     *
     * @param string $idChef
     * @return EquipeChef
     */
    public function setIdChef($idChef)
    {
        $this->idChef = $idChef;

        return $this;
    }

    /**
     * Get idChef
     *
     * @return string 
     */
    public function getIdChef()
    {
        return $this->idChef;
    }

    /**
     * Set dateD
     *
     * @param \DateTime $dateD
     * @return EquipeChef
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
}
