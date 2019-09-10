<?php

namespace App\Service;

use App\Entity\Article;
use Symfony\Component\Validator\Validation;

class ArticleValidator {
    public function validate(Article $article): array {
        $errors = [];

        // Construction du validateur
        $configPath = dirname(__DIR__, 2) . '/config/validation/article.yaml';
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        // Validation
        $violations = $validator->validate($article);

        // Reformatage des messages d'erreur dans un beau tableau
        foreach ($violations as $violation) {
            $key = $violation->getPropertyPath();
            $message = $violation->getMessage();
            $errors[$key] = $message;
        }

        return $errors;
    }
}
