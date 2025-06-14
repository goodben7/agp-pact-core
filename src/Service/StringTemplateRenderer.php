<?php

namespace App\Service;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Psr\Log\LoggerInterface;

class StringTemplateRenderer
{
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(private readonly LoggerInterface $logger)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableMagicCall()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    /**
     * Renders a template string by replacing placeholders with values from a context object.
     * Placeholders format:
     * - #{object.property.subProperty}
     * - #{object.id}
     * - #{object.date|format('d/m/Y')} for custom date formatting
     *
     * @param string $templateContent The string containing placeholders
     * @param object $context The primary object from which to resolve properties
     * @return string The rendered string.
     * @throws \ReflectionException
     */
    public function render(string $templateContent, object $context): string
    {
        // Regex pour trouver les placeholders #{object.property} avec support des formats optionnels
        return preg_replace_callback('/#\{([a-zA-Z0-9_\-\.]+)(\|format\([\'"]([^\'"]+)[\'"]\))?\}/', function ($matches) use ($context) {
            $propertyPath = $matches[1]; // Ex: "complaint.declarationDate"
            $customFormat = $matches[3] ?? null; // Ex: "d/m/Y" si format spécifié

            $parts = explode('.', $propertyPath);
            $currentObject = $context;
            $resolvedValue = null;

            foreach ($parts as $index => $part) {
                if ($index === 0) {
                    if (strtolower($part) === strtolower((new \ReflectionClass($context))->getShortName())) {
                        $currentObject = $context;
                    } elseif ($this->propertyAccessor->isReadable($context, $part)) {
                        $currentObject = $this->propertyAccessor->getValue($context, $part);
                    } else {
                        $this->logger->warning(sprintf('Cannot resolve placeholder "%s": Initial property "%s" not found on context object.', $propertyPath, $part));
                        return $matches[0];
                    }
                } else {
                    if ($currentObject === null) {
                        break;
                    }
                    if ($this->propertyAccessor->isReadable($currentObject, $part)) {
                        $currentObject = $this->propertyAccessor->getValue($currentObject, $part);
                    } else {
                        $this->logger->warning(sprintf('Cannot resolve placeholder "%s": Property "%s" not found on intermediate object.', $propertyPath, $part));
                        return $matches[0];
                    }
                }
            }

            $resolvedValue = $currentObject;

            // Formatter les DateTimeImmutable avec format personnalisé ou par défaut
            if ($resolvedValue instanceof \DateTimeInterface) {
                $format = $customFormat ?? 'd-m-Y H:i:s';
                return $resolvedValue->format($format);
            }

            // Gérer les objets pour retourner leur 'value' ou 'name'
            if (is_object($resolvedValue)) {
                if ($this->propertyAccessor->isReadable($resolvedValue, 'value')) {
                    return (string)$this->propertyAccessor->getValue($resolvedValue, 'value');
                }
                if ($this->propertyAccessor->isReadable($resolvedValue, 'name')) {
                    return (string)$this->propertyAccessor->getValue($resolvedValue, 'name');
                }
                return (string)$resolvedValue;
            }

            return (string)$resolvedValue;
        }, $templateContent);
    }
}
