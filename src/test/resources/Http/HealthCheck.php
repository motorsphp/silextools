<?php namespace Resource\Http;

use JMS\Serializer\Annotation;
use Symfony\Component\Validator\Constraints;
use Swagger\Annotations as SWG;

/**
 * @SWG\Definition(
 *   definition="HealthCheck",
 *   type="object",
 *   required={"status"}
 * )
 */
class HealthCheck
{
    /**
     * @SWG\Property(type="string")
     * @Annotation\Type("string")
     * @Constraints\NotBlank()
     * @Constraints\NotNull()
     * @var string
     */
    private $status;

    public function __construct(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
