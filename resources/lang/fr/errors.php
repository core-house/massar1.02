<?php

return [
    // 404 Page Not Found
    'page_not_found_title' => 'Page non trouvée 😅',
    'page_not_found_message' => 'La page que vous recherchez n\'existe pas !',
    'page_not_found_dialect' => 'Nous enregistrons les tentatives d\'accès depuis le lien ci-dessus comme suspectes - retournez à l\'accueil et utilisez les boutons !',
    'go_home' => 'Aller à l\'accueil',

    // 403 Access Forbidden
    'access_forbidden_title' => 'Zone restreinte 😠',
    'access_forbidden_message' => 'Désolé, vous n\'avez pas la permission d\'accéder à cette page.',
    'access_denied' => 'Accès refusé',
    'insufficient_permissions' => 'Permissions insuffisantes',
    'unauthorized_access' => 'Accès non autorisé',

    // Erreurs générales
    'error_occurred' => 'Une erreur est survenue',
    'unexpected_error' => 'Erreur inattendue',
    'system_error' => 'Erreur système',
    'database_error' => 'Erreur de base de données',
    'network_error' => 'Erreur réseau',
    'server_error' => 'Erreur serveur',
    'client_error' => 'Erreur client',

    // Erreurs d'authentification
    'authentication_error' => 'Erreur d\'authentification',
    'authorization_error' => 'Erreur d\'autorisation',
    'login_required' => 'Connexion requise',
    'invalid_credentials' => 'Identifiants invalides',
    'account_locked' => 'Compte verrouillé',
    'account_disabled' => 'Compte désactivé',
    'session_expired' => 'Session expirée',
    'token_expired' => 'Jeton expiré',
    'invalid_token' => 'Jeton invalide',

    // Erreurs de validation
    'validation_error' => 'Erreur de validation',
    'required_field' => 'Ce champ est requis',
    'invalid_format' => 'Format invalide',
    'minimum_length' => 'La longueur doit être d\'au moins',
    'maximum_length' => 'La longueur doit être d\'au plus',
    'invalid_email' => 'Adresse e-mail invalide',
    'invalid_phone' => 'Numéro de téléphone invalide',
    'invalid_date' => 'Date invalide',
    'invalid_number' => 'Nombre invalide',
    'invalid_url' => 'URL invalide',

    // Erreurs de fichiers
    'file_error' => 'Erreur de fichier',
    'file_not_found' => 'Fichier non trouvé',
    'file_upload_error' => 'Erreur de téléversement',
    'file_download_error' => 'Erreur de téléchargement',
    'file_too_large' => 'Fichier trop volumineux',
    'invalid_file_type' => 'Type de fichier invalide',
    'file_corrupted' => 'Fichier corrompu',
    'file_permission_denied' => 'Permission de fichier refusée',

    // Erreurs de base de données
    'database_connection_failed' => 'Échec de connexion à la base de données',
    'query_failed' => 'Échec de la requête',
    'record_not_found' => 'Enregistrement non trouvé',
    'duplicate_entry' => 'Entrée en double',
    'constraint_violation' => 'Violation de contrainte',
    'foreign_key_violation' => 'Violation de clé étrangère',
    'unique_constraint_violation' => 'Violation de contrainte unique',

    // Erreurs réseau
    'connection_failed' => 'Échec de connexion',
    'timeout_error' => 'Erreur de délai d\'attente',
    'server_unreachable' => 'Serveur inaccessible',
    'service_unavailable' => 'Service indisponible',
    'gateway_error' => 'Erreur de passerelle',
    'bad_gateway' => 'Mauvaise passerelle',
    'internal_server_error' => 'Erreur interne du serveur',

    // Erreurs de logique métier
    'business_rule_violation' => 'Violation de règle métier',
    'insufficient_funds' => 'Fonds insuffisants',
    'item_out_of_stock' => 'Article en rupture de stock',
    'order_already_processed' => 'Commande déjà traitée',
    'invalid_transaction' => 'Transaction invalide',
    'operation_not_allowed' => 'Opération non autorisée',
    'data_integrity_error' => 'Erreur d\'intégrité des données',

    // Erreurs d'interface utilisateur
    'ui_error' => 'Erreur d\'interface utilisateur',
    'form_validation_failed' => 'Échec de validation du formulaire',
    'invalid_input' => 'Saisie invalide',
    'missing_required_fields' => 'Champs requis manquants',
    'invalid_selection' => 'Sélection invalide',
    'action_cancelled' => 'Action annulée',
    'operation_failed' => 'Échec de l\'opération',

    // Erreurs de maintenance système
    'system_maintenance' => 'Maintenance système',
    'system_offline' => 'Système hors ligne',
    'maintenance_mode' => 'Mode maintenance',
    'system_overloaded' => 'Système surchargé',
    'service_degraded' => 'Service dégradé',
    'planned_maintenance' => 'Maintenance planifiée',
    'emergency_maintenance' => 'Maintenance d\'urgence',

    // Erreurs de sécurité
    'security_violation' => 'Violation de sécurité',
    'suspicious_activity' => 'Activité suspecte',
    'rate_limit_exceeded' => 'Limite de taux dépassée',
    'too_many_requests' => 'Trop de requêtes',
    'ip_blocked' => 'IP bloquée',
    'account_suspended' => 'Compte suspendu',
    'security_check_failed' => 'Échec de vérification de sécurité',

    // Erreurs de ressources
    'resource_not_found' => 'Ressource non trouvée',
    'resource_unavailable' => 'Ressource indisponible',
    'resource_busy' => 'Ressource occupée',
    'resource_locked' => 'Ressource verrouillée',
    'forbidden' => 'Interdit',

    // Erreurs de délai d'attente
    'request_timeout' => 'Délai d\'attente de requête',
    'operation_timeout' => 'Délai d\'attente d\'opération',
    'connection_timeout' => 'Délai d\'attente de connexion',
    'response_timeout' => 'Délai d\'attente de réponse',
    'processing_timeout' => 'Délai d\'attente de traitement',
    'session_timeout' => 'Délai d\'attente de session',
    'idle_timeout' => 'Délai d\'attente d\'inactivité',

    // Erreurs de configuration
    'configuration_error' => 'Erreur de configuration',
    'missing_configuration' => 'Configuration manquante',
    'invalid_configuration' => 'Configuration invalide',
    'configuration_file_not_found' => 'Fichier de configuration non trouvé',
    'configuration_parse_error' => 'Erreur d\'analyse de configuration',
    'environment_error' => 'Erreur d\'environnement',

    // Erreurs de services tiers
    'external_service_error' => 'Erreur de service externe',
    'api_error' => 'Erreur d\'API',
    'external_service_timeout' => 'Délai d\'attente de service externe',
    'external_service_failed' => 'Échec de service externe',
    'integration_error' => 'Erreur d\'intégration',

    // Messages de récupération
    'try_again_later' => 'Veuillez réessayer plus tard',
    'contact_support' => 'Veuillez contacter le support',
    'check_connection' => 'Veuillez vérifier votre connexion',
    'refresh_page' => 'Veuillez actualiser la page',
    'clear_cache' => 'Veuillez vider votre cache',
    'try_different_browser' => 'Veuillez essayer un navigateur différent',
    'check_permissions' => 'Veuillez vérifier vos permissions',
];
