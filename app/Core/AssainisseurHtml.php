<?php

declare(strict_types=1);

namespace App\Core;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * Nettoie le HTML produit par l'éditeur enrichi côté navigateur avant tout
 * enregistrement en base. On ne fait jamais confiance au HTML reçu, même
 * envoyé par un compte de confiance : une requête directe (hors éditeur) ou
 * un bug côté navigateur pourrait sinon injecter du contenu actif dans des
 * pages vues par tous les visiteurs du site.
 */
final class AssainisseurHtml
{
    private const BALISES_AUTORISEES = ['p', 'br', 'strong', 'em', 'h2', 'h3', 'ul', 'ol', 'li', 'a'];
    private const BALISES_SUPPRIMEES_ENTIEREMENT = ['script', 'style'];

    public static function nettoyer(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="utf-8" ?><div id="racine-assainissement">' . $html . '</div>',
            LIBXML_NOERROR | LIBXML_NOWARNING
        );
        libxml_clear_errors();

        $racine = $document->getElementById('racine-assainissement');
        if ($racine === null) {
            return '';
        }

        $resultat = '';
        foreach (iterator_to_array($racine->childNodes) as $enfant) {
            $resultat .= self::nettoyerNoeud($enfant);
        }

        return trim($resultat);
    }

    private static function nettoyerNoeud(DOMNode $noeud): string
    {
        if ($noeud instanceof DOMText) {
            return htmlspecialchars($noeud->textContent, ENT_QUOTES, 'UTF-8');
        }

        if (!$noeud instanceof DOMElement) {
            return '';
        }

        $nomBalise = strtolower($noeud->nodeName);

        if (in_array($nomBalise, self::BALISES_SUPPRIMEES_ENTIEREMENT, true)) {
            return '';
        }

        $contenuEnfants = '';
        foreach (iterator_to_array($noeud->childNodes) as $enfant) {
            $contenuEnfants .= self::nettoyerNoeud($enfant);
        }

        if (!in_array($nomBalise, self::BALISES_AUTORISEES, true)) {
            // Balise non autorisée : on garde le contenu, pas la balise.
            return $contenuEnfants;
        }

        if ($nomBalise === 'br') {
            return '<br>';
        }

        $attributs = '';
        if ($nomBalise === 'a') {
            $href = $noeud->getAttribute('href');
            if (self::hrefAutorise($href)) {
                $attributs = ' href="' . htmlspecialchars($href, ENT_QUOTES, 'UTF-8') . '" rel="noopener"';
            }
        }

        return "<{$nomBalise}{$attributs}>{$contenuEnfants}</{$nomBalise}>";
    }

    private static function hrefAutorise(string $href): bool
    {
        $href = trim($href);
        if ($href === '') {
            return false;
        }

        return str_starts_with($href, 'http://')
            || str_starts_with($href, 'https://')
            || str_starts_with($href, 'mailto:')
            || str_starts_with($href, '/');
    }
}
