<?php
/**
 * @copyright 2012 Instaclick Inc.
 */

namespace IC\Bundle\Base\ComponentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use JMS\Serializer\Annotation as Rest;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Base Entity for all other system entities
 *
 * @ORM\MappedSuperclass
 *
 * @author Guilherme Blanco <gblanco@nationalfibre.net>
 * @author Oleksandr Kovalov <oleksandrk@nationalfibre.net>
 */
abstract class Entity
{
    /**
     * @ORM\Column(type="datetime", options={ "comment"="creation date and time" })
     *
     * @Rest\Type("DateTime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", options={ "comment"="last modification date and time" })
     *
     * @Rest\Type("DateTime")
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
     */
    protected $updated;

    /**
     * Get the created date.
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Get the updated date.
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
