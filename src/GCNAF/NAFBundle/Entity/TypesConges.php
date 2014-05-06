<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TypesConges
 *
 * @ORM\Table(name="types_conges")
 * @ORM\Entity
 */
class TypesConges
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_types", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTypes;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_cng", type="text", nullable=false)
     */
    private $nomCng;

    /**
     * @var string
     *
     * @ORM\Column(name="duree_max", type="string", length=255, nullable=false)
     */
    private $dureeMax;



    /**
     * Get idTypes
     *
     * @return integer 
     */
    public function getIdTypes()
    {
        return $this->idTypes;
    }

    /**
     * Set nomCng
     *
     * @param string $nomCng
     * @return TypesConges
     */
    public function setNomCng($nomCng)
    {
        $this->nomCng = $nomCng;

        return $this;
    }

    /**
     * Get nomCng
     *
     * @return string 
     */
    public function getNomCng()
    {
        return $this->nomCng;
    }

    /**
     * Set dureeMax
     *
     * @param string $dureeMax
     * @return TypesConges
     */
    public function setDureeMax($dureeMax)
    {
        $this->dureeMax = $dureeMax;

        return $this;
    }

    /**
     * Get dureeMax
     *
     * @return string 
     */
    public function getDureeMax()
    {
        return $this->dureeMax;
    }
}
