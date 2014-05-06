<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * JoursFeries
 *
 * @ORM\Table(name="jours_feries")
 * @ORM\Entity
 */
class JoursFeries
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ref_jf", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $refJf;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_projet", type="integer", nullable=false)
     */
    private $idProjet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=false)
     */
    private $date;



    /**
     * Get refJf
     *
     * @return integer 
     */
    public function getRefJf()
    {
        return $this->refJf;
    }

    /**
     * Set idProjet
     *
     * @param integer $idProjet
     * @return JoursFeries
     */
    public function setIdProjet($idProjet)
    {
        $this->idProjet = $idProjet;

        return $this;
    }

    /**
     * Get idProjet
     *
     * @return integer 
     */
    public function getIdProjet()
    {
        return $this->idProjet;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return JoursFeries
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
}
