<?php

namespace GCNAF\NAFBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EtatConge
 *
 * @ORM\Table(name="etat_conge")
 * @ORM\Entity
 */
class EtatConge
{
    /**
     * @var string
     *
     * @ORM\Column(name="id_etat", type="string", length=20, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEtat;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_etat", type="string", length=120, nullable=false)
     */
    private $nomEtat;



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
     * Set nomEtat
     *
     * @param string $nomEtat
     * @return EtatConge
     */
    public function setNomEtat($nomEtat)
    {
        $this->nomEtat = $nomEtat;

        return $this;
    }

    /**
     * Get nomEtat
     *
     * @return string 
     */
    public function getNomEtat()
    {
        return $this->nomEtat;
    }

	public function __toString() {
	
		return $this->getNomEtat();
	}
}
