#!/usr/bin/env bash
set -euo pipefail

mkdir -p documentation/preuves
OUT="documentation/preuves/performance_mesures.csv"

echo 'date,application,action,url_ou_action,code_http,temps_secondes,objectif_secondes,statut,preuve,commentaire' > "$OUT"

measure() {
  local app="$1"
  local action="$2"
  local url="$3"
  local objectif="$4"
  local now
  now=$(date +%F)
  local tmp
  tmp=$(mktemp)
  local result
  result=$(curl -o /dev/null -s -w '%{http_code};%{time_total}' "$url" || echo '000;0')
  local code="${result%;*}"
  local time="${result#*;}"
  local status='Non conforme'

  if [[ "$code" == "200" || "$code" == "302" ]]; then
    status='À analyser'
  fi

  echo "$now,$app,$action,$url,$code,$time,$objectif,$status,performance_mesures.csv,Mesure automatique curl" >> "$OUT"
}

measure 'questionnaire' 'page_accueil_ou_login' 'http://localhost:8080' '2'
measure 'event-app' 'dashboard_logs' 'http://localhost:8081' '3'

echo "Mesures écrites dans $OUT"
