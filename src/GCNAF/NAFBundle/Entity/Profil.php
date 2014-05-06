<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profil
 *
 * @ORM\Table(name="profil")
 * @ORM\Entity
 */
class Profil
{
    /**
     * @var string
     *
     * @ORM\Column(name="id_prof", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idProf;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_prof", type="string", length=120, nullable=false)
     */
    private $nomProf;



    /**
     * Get idProf
     *
     * @return string 
     */
    public function getIdProf()
    {
        return $this->idProf;
    }

    /**
     * Set nomProf
     *
     * @param string $nomProf
     * @return Profil
     */
    public function setNomProf($nomProf)
    {
        $this->nomProf = $nomProf;

        return $this;
    }

    /**
     * Get nomProf
     *
     * @return string 
     */
    public function getNomProf()
    {
        return $this->nomProf;
    }
}
