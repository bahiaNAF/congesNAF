<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cra
 *
 * @ORM\Table(name="cra")
 * @ORM\Entity
 */
class Cra
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
     * @var integer
     *
     * @ORM\Column(name="salarie", type="integer", nullable=false)
     */
    private $salarie;

    /**
     * @var integer
     *
     * @ORM\Column(name="tache", type="integer", nullable=false)
     */
    private $tache;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="duree", type="float", precision=10, scale=0, nullable=false)
     */
    private $duree;

    /**
     * @var string
     *
     * @ORM\Column(name="remarque", type="text", nullable=false)
     */
    private $remarque;



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
     * Set salarie
     *
     * @param integer $salarie
     * @return Cra
     */
    public function setSalarie($salarie)
    {
        $this->salarie = $salarie;

        return $this;
    }

    /**
     * Get salarie
     *
     * @return integer 
     */
    public function getSalarie()
    {
        return $this->salarie;
    }

    /**
     * Set tache
     *
     * @param integer $tache
     * @return Cra
     */
    public function setTache($tache)
    {
        $this->tache = $tache;

        return $this;
    }

    /**
     * Get tache
     *
     * @return integer 
     */
    public function getTache()
    {
        return $this->tache;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Cra
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set duree
     *
     * @param float $duree
     * @return Cra
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * Get duree
     *
     * @return float 
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * Set remarque
     *
     * @param string $remarque
     * @return Cra
     */
    public function setRemarque($remarque)
    {
        $this->remarque = $remarque;

        return $this;
    }

    /**
     * Get remarque
     *
     * @return string 
     */
    public function getRemarque()
    {
        return $this->remarque;
    }
}
