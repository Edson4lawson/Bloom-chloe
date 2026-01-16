# 🔐 CHECKLIST DE SÉCURITÉ - Bloom-Chloe

## 📋 À Vérifier AVANT Mise en Production

Cette checklist doit être complétée avant chaque déploiement en production.

---

## 1. ✅ Configuration Environnement

- [ ] Fichier `.env` créé à partir de `.env.example`
- [ ] `APP_ENV=production` configuré
- [ ] `APP_DEBUG=false` configuré
- [ ] Tous les mots de passe changés (pas de valeurs par défaut)
- [ ] `JWT_SECRET` généré avec `openssl rand -base64 64`
- [ ] `PAYMENT_SECRET_KEY` généré et unique
- [ ] `.env` ajouté au `.gitignore`
- [ ] Aucun secret dans le code source ou les commits Git

---

## 2. ✅ Base de Données

- [ ] Utilisateur MySQL dédié (pas `root`)
- [ ] Mot de passe MySQL fort (16+ caractères, mixte)
- [ ] Connexion MySQL en SSL (production)
- [ ] Accès distant désactivé ou limité par IP
- [ ] Sauvegardes automatiques configurées
- [ ] Logs SQL activés pour audit

---

## 3. ✅ Authentification & Sessions

- [ ] Politique de mot de passe implémentée (8+ char, majuscule, chiffre, spécial)
- [ ] Rate limiting sur `/api/auth/login.php` (5 tentatives/5 min)
- [ ] Rate limiting sur `/api/auth/register.php` (3/heure)
- [ ] Tokens avec expiration courte (15 min access, 30 jours refresh)
- [ ] Refresh tokens implémentés
- [ ] Invalidation des tokens à la déconnexion
- [ ] Logs des connexions réussies/échouées

---

## 4. ✅ Headers de Sécurité HTTP

- [ ] `Content-Security-Policy` configuré
- [ ] `X-Frame-Options: DENY`
- [ ] `X-Content-Type-Options: nosniff`
- [ ] `X-XSS-Protection: 1; mode=block`
- [ ] `Referrer-Policy: strict-origin-when-cross-origin`
- [ ] `Permissions-Policy` configuré
- [ ] `Strict-Transport-Security` (HSTS) activé

Vérifier avec: https://securityheaders.com

---

## 5. ✅ CORS

- [ ] `Access-Control-Allow-Origin: *` remplacé par liste blanche
- [ ] Origines de production explicitement listées
- [ ] `Access-Control-Allow-Credentials: true` uniquement si nécessaire
- [ ] Preflight caching activé (`Access-Control-Max-Age`)

---

## 6. ✅ HTTPS / TLS

- [ ] Certificat SSL valide installé
- [ ] Redirection HTTP → HTTPS forcée
- [ ] TLS 1.2+ uniquement (TLS 1.0/1.1 désactivés)
- [ ] Ciphers faibles désactivés
- [ ] HSTS activé avec `includeSubDomains`
- [ ] Certificat dans Certificate Transparency

Vérifier avec: https://www.ssllabs.com/ssltest/

---

## 7. ✅ Protection des Données

- [ ] Mots de passe hashés avec bcrypt/Argon2id
- [ ] Données sensibles chiffrées en base
- [ ] Pas de données sensibles dans les URLs
- [ ] Pas de données sensibles dans les logs
- [ ] RGPD : consentement cookies implémenté
- [ ] RGPD : droit à l'effacement implémenté

---

## 8. ✅ Validation des Entrées

- [ ] Toutes les entrées utilisateur validées côté serveur
- [ ] Requêtes SQL préparées (pas de concaténation)
- [ ] XSS : données échappées avant affichage
- [ ] Upload fichiers : types MIME vérifiés
- [ ] Upload fichiers : taille limitée
- [ ] Upload fichiers : stockés hors webroot

---

## 9. ✅ Protections Réseau

- [ ] WAF configuré (Cloudflare, ModSecurity)
- [ ] Protection DDoS active
- [ ] Rate limiting global API (100 req/min)
- [ ] Fail2ban configuré sur le serveur
- [ ] Ports non essentiels fermés
- [ ] SSH par clé uniquement (pas de mot de passe)

---

## 10. ✅ Monitoring & Logging

- [ ] Logs d'accès Nginx/Apache activés
- [ ] Logs d'erreurs PHP activés
- [ ] Logs de sécurité centralisés
- [ ] Alertes sur tentatives de brute force
- [ ] Alertes sur erreurs 500 fréquentes
- [ ] Rotation des logs configurée

---

## 11. ✅ Dépendances

- [ ] `npm audit` exécuté sans vulnérabilités critiques
- [ ] Dépendances PHP vérifiées
- [ ] Mises à jour automatiques de sécurité configurées
- [ ] Versions PHP/Node.js supportées

---

## 12. ✅ Tests de Sécurité

- [ ] Scan OWASP ZAP exécuté
- [ ] Test de pénétration effectué
- [ ] Revue de code sécurité effectuée
- [ ] Tests de charge effectués

---

## 🛠️ Outils Recommandés

### Scanners de Vulnérabilités
- **OWASP ZAP** : https://www.zaproxy.org/
- **Nikto** : https://cirt.net/Nikto2
- **SQLMap** : https://sqlmap.org/

### Vérification Headers
- **Security Headers** : https://securityheaders.com
- **Mozilla Observatory** : https://observatory.mozilla.org

### SSL/TLS
- **SSL Labs** : https://www.ssllabs.com/ssltest/

### WAF & Anti-DDoS
- **Cloudflare** : https://cloudflare.com (gratuit)
- **AWS WAF** : https://aws.amazon.com/waf/
- **ModSecurity** : https://modsecurity.org/

### Monitoring
- **Sentry** : https://sentry.io (erreurs)
- **UptimeRobot** : https://uptimerobot.com (disponibilité)
- **Fail2ban** : https://www.fail2ban.org/ (intrusion)

---

## 📊 Scores de Sécurité Cibles

| Outil | Score Minimum |
|-------|---------------|
| Security Headers | A+ |
| SSL Labs | A+ |
| Mozilla Observatory | A+ |
| OWASP ZAP | 0 alertes High/Medium |

---

## 📅 Maintenance Continue

- [ ] **Hebdomadaire** : Vérifier `npm audit`
- [ ] **Mensuelle** : Renouveler les tokens admin
- [ ] **Trimestrielle** : Revue des accès utilisateurs
- [ ] **Semestrielle** : Audit de sécurité complet
- [ ] **Annuelle** : Test de pénétration professionnel

---

## 🚨 En Cas d'Incident

1. **Isoler** : Mettre le site en maintenance
2. **Identifier** : Analyser les logs
3. **Contenir** : Révoquer les tokens compromis
4. **Notifier** : Informer les utilisateurs si nécessaire (RGPD)
5. **Corriger** : Appliquer les correctifs
6. **Documenter** : Rapport d'incident

Contact urgence sécurité : security@bloom-chloe.com

---

*Dernière mise à jour : 16 janvier 2026*
