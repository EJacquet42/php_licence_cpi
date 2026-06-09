<?php

return [
    'questions' => [
        [
            'question' => "Quel est le rôle principal de rsyslog sous Linux ?",
            'answers' => [
                "Gérer les utilisateurs du système",
                "Collecter, filtrer et transmettre les logs",
                "Installer les paquets système",
                "Surveiller uniquement l'espace disque",
            ],
            'correct' => 1,
            'explanation' => "rsyslog sert à collecter, traiter, filtrer, stocker et éventuellement envoyer les logs vers un serveur distant.",
        ],
        [
            'question' => "Quel fichier est généralement utilisé pour configurer rsyslog ?",
            'answers' => [
                "/etc/hosts",
                "/etc/rsyslog.conf",
                "/var/log/syslog.conf",
                "/etc/logrotate.conf",
            ],
            'correct' => 1,
            'explanation' => "Le fichier principal de configuration est souvent /etc/rsyslog.conf.",
        ],
        [
            'question' => "Dans quel dossier trouve-t-on généralement les fichiers de configuration additionnels de rsyslog ?",
            'answers' => [
                "/etc/rsyslog.d/",
                "/var/rsyslog/",
                "/home/rsyslog/",
                "/usr/logs/",
            ],
            'correct' => 0,
            'explanation' => "Les fichiers personnalisés sont généralement placés dans /etc/rsyslog.d/.",
        ],
        [
            'question' => "Quel fichier contient souvent les logs système généraux sur Debian/Ubuntu ?",
            'answers' => [
                "/var/log/auth.log",
                "/var/log/syslog",
                "/var/log/messages",
                "/var/log/secure",
            ],
            'correct' => 1,
            'explanation' => "Sur Debian/Ubuntu, les logs système généraux sont souvent dans /var/log/syslog.",
        ],
        [
            'question' => "Quel fichier contient généralement les logs d'authentification sur Debian/Ubuntu ?",
            'answers' => [
                "/var/log/auth.log",
                "/var/log/kern.log",
                "/var/log/boot.log",
                "/var/log/dpkg.log",
            ],
            'correct' => 0,
            'explanation' => "Les connexions, sudo, SSH et événements d'authentification sont souvent dans /var/log/auth.log.",
        ],
        [
            'question' => "Dans une règle rsyslog, que représente la partie avant le point dans auth.info ?",
            'answers' => [
                "La priorité",
                "La facility",
                "Le nom du fichier",
                "Le protocole réseau",
            ],
            'correct' => 1,
            'explanation' => "Dans auth.info, auth est la facility et info est la priorité.",
        ],
        [
            'question' => "Dans une règle rsyslog, que représente la partie après le point dans auth.info ?",
            'answers' => [
                "La facility",
                "Le nom de service",
                "La priorité",
                "Le port réseau",
            ],
            'correct' => 2,
            'explanation' => "Dans auth.info, info correspond au niveau de gravité, aussi appelé priorité.",
        ],
        [
            'question' => "Quelle priorité indique un événement critique nécessitant une attention immédiate ?",
            'answers' => [
                "debug",
                "info",
                "notice",
                "emerg",
            ],
            'correct' => 3,
            'explanation' => "emerg est le niveau le plus grave : le système est inutilisable ou dans un état critique.",
        ],
        [
            'question' => "Quelle priorité est généralement utilisée pour les messages très détaillés de diagnostic ?",
            'answers' => [
                "debug",
                "warning",
                "crit",
                "alert",
            ],
            'correct' => 0,
            'explanation' => "debug est utilisé pour les informations détaillées utiles au diagnostic.",
        ],
        [
            'question' => "Que signifie la règle suivante : *.info /var/log/messages ?",
            'answers' => [
                "Tous les messages de niveau info et supérieur sont écrits dans /var/log/messages",
                "Seuls les messages du service info sont écrits",
                "Tous les logs sont supprimés",
                "Le fichier /var/log/messages est désactivé",
            ],
            'correct' => 0,
            'explanation' => "*.info signifie toutes les facilities avec une priorité info ou plus grave.",
        ],
        [
            'question' => "Quel symbole est utilisé pour envoyer des logs vers un serveur distant en UDP ?",
            'answers' => [
                "@",
                "@@",
                "#",
                "\$",
            ],
            'correct' => 0,
            'explanation' => "Un seul @ indique généralement un envoi en UDP.",
        ],
        [
            'question' => "Quel symbole est utilisé pour envoyer des logs vers un serveur distant en TCP ?",
            'answers' => [
                "@",
                "@@",
                "tcp://",
                "%",
            ],
            'correct' => 1,
            'explanation' => "Deux @@ indiquent généralement un envoi en TCP.",
        ],
        [
            'question' => "Quelle ligne permettrait d'envoyer tous les logs vers un serveur distant en UDP ?",
            'answers' => [
                "*.* @192.168.1.10:514",
                "*.* @@192.168.1.10:514",
                "logs.send 192.168.1.10",
                "/var/log/* 192.168.1.10",
            ],
            'correct' => 0,
            'explanation' => "La syntaxe @adresse:port permet l'envoi en UDP.",
        ],
        [
            'question' => "Quel port est traditionnellement utilisé par syslog/rsyslog ?",
            'answers' => [
                "22",
                "80",
                "443",
                "514",
            ],
            'correct' => 3,
            'explanation' => "Le port traditionnel de syslog est le port 514.",
        ],
        [
            'question' => "Quelle commande permet de redémarrer le service rsyslog sur un système utilisant systemd ?",
            'answers' => [
                "systemctl restart rsyslog",
                "restart rsyslog.service now",
                "rsyslog --reload",
                "service logs restart",
            ],
            'correct' => 0,
            'explanation' => "Avec systemd, on utilise généralement systemctl restart rsyslog.",
        ],
        [
            'question' => "Quelle commande permet de vérifier l'état du service rsyslog ?",
            'answers' => [
                "systemctl status rsyslog",
                "rsyslog check",
                "cat /etc/rsyslog.conf",
                "journalctl enable rsyslog",
            ],
            'correct' => 0,
            'explanation' => "systemctl status rsyslog permet de voir si le service est actif ou en erreur.",
        ],
        [
            'question' => "À quoi sert logrotate ?",
            'answers' => [
                "À chiffrer les logs",
                "À supprimer rsyslog",
                "À faire tourner, compresser et nettoyer les anciens fichiers de logs",
                "À envoyer les logs vers un serveur DNS",
            ],
            'correct' => 2,
            'explanation' => "logrotate évite que les fichiers de logs deviennent trop volumineux.",
        ],
        [
            'question' => "Quelle commande permet de lire les logs du journal systemd ?",
            'answers' => [
                "journalctl",
                "syslogctl",
                "logread",
                "readlogs",
            ],
            'correct' => 0,
            'explanation' => "journalctl permet de consulter les logs gérés par systemd-journald.",
        ],
        [
            'question' => "Quelle facility est souvent utilisée pour les messages liés à l'authentification ?",
            'answers' => [
                "mail",
                "auth",
                "daemon",
                "cron",
            ],
            'correct' => 1,
            'explanation' => "auth est souvent utilisée pour les événements liés à l'authentification.",
        ],
        [
            'question' => "Pourquoi centraliser les logs sur un serveur distant ?",
            'answers' => [
                "Pour accélérer le démarrage du serveur",
                "Pour éviter d'installer rsyslog",
                "Pour faciliter l'analyse, la sécurité et la conservation des logs",
                "Pour remplacer le pare-feu",
            ],
            'correct' => 2,
            'explanation' => "La centralisation facilite la supervision, la recherche d'incidents et la conservation des preuves.",
        ],
    ],
];
