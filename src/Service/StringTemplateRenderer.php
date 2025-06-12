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
     * Placeholders format: #{object.property.subProperty} or #{object.id}
     *
     * @param string $templateContent The string containing placeholders (e.g., "Plainte #{complaint.id} de #{complainant.fullName}")
     * @param object $context The primary object from which to resolve properties (e.g., a Complaint entity).
     * @return string The rendered string.
     * @throws \ReflectionException
     */
    public function render(string $templateContent, object $context): string
    {
        // Regex pour trouver les placeholders #{object.property}
        return preg_replace_callback('/#\{([a-zA-Z0-9_\-\.]+)\}/', function ($matches) use ($context) {
            $propertyPath = $matches[1]; // Ex: "complaint.id" ou "complainant.fullName"
            $parts = explode('.', $propertyPath);

            $currentObject = $context;
            $resolvedValue = null;

            foreach ($parts as $index => $part) {
                // Pour la première partie (ex: "complaint" ou "complainant"), on essaie de la récupérer
                // à partir du contexte si le nom correspond, ou si c'est le contexte lui-même.
                // Sinon, si c'est une relation, on essaie de l'obtenir via un getter.
                if ($index === 0) {
                    if (strtolower($part) === strtolower((new \ReflectionClass($context))->getShortName())) {
                        // Si la première partie du chemin est le nom court de l'objet de contexte (ex: 'complaint' pour une Complaint entity)
                        $currentObject = $context;
                    } elseif ($this->propertyAccessor->isReadable($context, $part)) {
                        // Si la première partie est une propriété ou une relation de l'objet de contexte
                        $currentObject = $this->propertyAccessor->getValue($context, $part);
                    } else {
                        // Si la première partie n'est pas l'objet de contexte et n'est pas une propriété/relation,
                        // cela signifie qu'elle est probablement une relation non chargée ou invalide.
                        $this->logger->warning(sprintf('Cannot resolve placeholder "%s": Initial property "%s" not found on context object.', $propertyPath, $part));
                        return $matches[0]; // Retourne le placeholder tel quel
                    }
                } else {
                    // Pour les parties suivantes, on accède aux propriétés de l'objet courant
                    if ($currentObject === null) {
                        break; // Si un objet précédent est null, on ne peut pas continuer
                    }
                    if ($this->propertyAccessor->isReadable($currentObject, $part)) {
                        $currentObject = $this->propertyAccessor->getValue($currentObject, $part);
                    } else {
                        $this->logger->warning(sprintf('Cannot resolve placeholder "%s": Property "%s" not found on intermediate object.', $propertyPath, $part));
                        return $matches[0]; // Retourne le placeholder tel quel
                    }
                }
            }

            $resolvedValue = $currentObject;

            // Formatter les DateTimeImmutable si besoin
            if ($resolvedValue instanceof \DateTimeInterface) {
                // Vous pouvez ajouter une logique de formatage plus sophistiquée ici
                // Par exemple, en fonction de la langue de l'utilisateur ou d'un format spécifique dans le placeholder (ex: #{date|format('Y-m-d')})
                return $resolvedValue->format('Y-m-d H:i:s');
            }

            // Gérer les objets (ex: GeneralParameter) pour retourner leur 'value' ou 'name'
            if (is_object($resolvedValue)) {
                if ($this->propertyAccessor->isReadable($resolvedValue, 'value')) {
                    return (string)$this->propertyAccessor->getValue($resolvedValue, 'value');
                }
                if ($this->propertyAccessor->isReadable($resolvedValue, 'name')) {
                    return (string)$this->propertyAccessor->getValue($resolvedValue, 'name');
                }
                // Si c'est un objet sans 'value' ou 'name', convertissez-le en chaîne (par exemple, un UUID)
                return (string)$resolvedValue;
            }

            return (string)$resolvedValue; // Convertir tout le reste en string
        }, $templateContent);
    }
}
