#!/bin/bash
# Scrape ALL PDFs from cuadromedico.de by crawling every province page
# and extracting the real PDF URL from the HTML

PDF_DIR="/var/www/micuadromedico/public/pdfs"
LOG="/var/www/micuadromedico/storage/logs/pdf-scrape.log"
MAPPING="/var/www/micuadromedico/storage/pdf-mapping.tsv"
mkdir -p "$PDF_DIR"

echo "$(date): Starting full scrape of cuadromedico.de" > "$LOG"

# All insurer slugs on cuadromedico.de
INSURERS="adeslas sanitas asisa dkv mapfre aegon asefa axa antares caser generali cigna agrupacio-mutua catalana-occidente cosalud acunsa fiatc dkv-la-fuencisla hna igualatorio-cantabria imq-asturias aegon-la-sanitaria aegon-labor-medica musa nectar allianz nortehispana plus-ultra previsora-general seguros-bilbao zurich vivaz union-madrilena"

# Province slugs as used by cuadromedico.de (no hyphens, no accents)
PROVINCES="acoruna alava albacete alicante almeria asturias avila badajoz baleares barcelona burgos caceres cadiz cantabria castellon ceuta ciudadreal cordoba cuenca girona granada guadalajara guipuzcoa huelva huesca jaen larioja laspalmas leon lleida lugo madrid malaga melilla murcia navarra ourense palencia pontevedra salamanca santacruzdetenerife segovia sevilla soria tarragona teruel toledo valencia valladolid vizcaya zamora zaragoza"

# Our province slug mapping (cuadromedico.de slug → our slug)
declare -A PROV_MAP
PROV_MAP[acoruna]="a-coruna"
PROV_MAP[alava]="alava"
PROV_MAP[albacete]="albacete"
PROV_MAP[alicante]="alicante"
PROV_MAP[almeria]="almeria"
PROV_MAP[asturias]="asturias"
PROV_MAP[avila]="avila"
PROV_MAP[badajoz]="badajoz"
PROV_MAP[baleares]="baleares"
PROV_MAP[barcelona]="barcelona"
PROV_MAP[burgos]="burgos"
PROV_MAP[caceres]="caceres"
PROV_MAP[cadiz]="cadiz"
PROV_MAP[cantabria]="cantabria"
PROV_MAP[castellon]="castellon"
PROV_MAP[ceuta]="ceuta"
PROV_MAP[ciudadreal]="ciudad-real"
PROV_MAP[cordoba]="cordoba"
PROV_MAP[cuenca]="cuenca"
PROV_MAP[girona]="girona"
PROV_MAP[granada]="granada"
PROV_MAP[guadalajara]="guadalajara"
PROV_MAP[guipuzcoa]="guipuzcoa"
PROV_MAP[huelva]="huelva"
PROV_MAP[huesca]="huesca"
PROV_MAP[jaen]="jaen"
PROV_MAP[larioja]="la-rioja"
PROV_MAP[laspalmas]="las-palmas"
PROV_MAP[leon]="leon"
PROV_MAP[lleida]="lleida"
PROV_MAP[lugo]="lugo"
PROV_MAP[madrid]="madrid"
PROV_MAP[malaga]="malaga"
PROV_MAP[melilla]="melilla"
PROV_MAP[murcia]="murcia"
PROV_MAP[navarra]="navarra"
PROV_MAP[ourense]="ourense"
PROV_MAP[palencia]="palencia"
PROV_MAP[pontevedra]="pontevedra"
PROV_MAP[salamanca]="salamanca"
PROV_MAP[santacruzdetenerife]="santa-cruz-de-tenerife"
PROV_MAP[segovia]="segovia"
PROV_MAP[sevilla]="sevilla"
PROV_MAP[soria]="soria"
PROV_MAP[tarragona]="tarragona"
PROV_MAP[teruel]="teruel"
PROV_MAP[toledo]="toledo"
PROV_MAP[valencia]="valencia"
PROV_MAP[valladolid]="valladolid"
PROV_MAP[vizcaya]="vizcaya"
PROV_MAP[zamora]="zamora"
PROV_MAP[zaragoza]="zaragoza"

TOTAL=0
DOWNLOADED=0
FAILED=0
SKIPPED=0

> "$MAPPING"

for insurer in $INSURERS; do
    for prov in $PROVINCES; do
        our_prov="${PROV_MAP[$prov]}"
        [ -z "$our_prov" ] && continue
        
        local_file="cuadro-medico-${insurer}-${our_prov}.pdf"
        
        # Skip if already have a good PDF
        if [ -f "$PDF_DIR/$local_file" ] && [ $(stat -f%z "$PDF_DIR/$local_file" 2>/dev/null || stat -c%s "$PDF_DIR/$local_file" 2>/dev/null) -gt 10000 ]; then
            SKIPPED=$((SKIPPED+1))
            TOTAL=$((TOTAL+1))
            continue
        fi
        
        # Fetch the province page and extract PDF URL
        page_url="https://cuadromedico.de/${insurer}-${prov}"
        pdf_path=$(curl -sL --max-time 10 "$page_url" | grep -oP '(?<=file=|href=")[^"]*\.pdf' | head -1)
        
        if [ -n "$pdf_path" ]; then
            # Make absolute URL
            if [[ "$pdf_path" == /* ]]; then
                pdf_url="https://cuadromedico.de${pdf_path}"
            else
                pdf_url="$pdf_path"
            fi
            
            # Download the PDF
            http_code=$(curl -sL -o "$PDF_DIR/$local_file" -w "%{http_code}" --max-time 60 "$pdf_url")
            filesize=$(stat -c%s "$PDF_DIR/$local_file" 2>/dev/null || echo 0)
            
            if [ "$http_code" = "200" ] && [ "$filesize" -gt 10000 ]; then
                DOWNLOADED=$((DOWNLOADED+1))
                echo -e "${insurer}\t${our_prov}\t/pdfs/${local_file}\t${pdf_url}" >> "$MAPPING"
            else
                rm -f "$PDF_DIR/$local_file"
                FAILED=$((FAILED+1))
            fi
        else
            FAILED=$((FAILED+1))
        fi
        
        TOTAL=$((TOTAL+1))
        
        # Progress every 100
        if [ $((TOTAL % 100)) -eq 0 ]; then
            echo "$(date): Progress: $TOTAL total, $DOWNLOADED new, $SKIPPED existing, $FAILED failed" >> "$LOG"
            echo "Progress: $TOTAL total, $DOWNLOADED new, $SKIPPED existing, $FAILED failed"
        fi
        
        sleep 0.2
    done
done

echo "$(date): DONE — $TOTAL total, $DOWNLOADED new downloads, $SKIPPED already existed, $FAILED not found" >> "$LOG"
echo ""
echo "=== FINAL ==="
echo "Total checked: $TOTAL"
echo "New downloads: $DOWNLOADED"
echo "Already existed: $SKIPPED"
echo "Not found: $FAILED"
echo "PDFs on disk: $(ls $PDF_DIR/*.pdf 2>/dev/null | wc -l)"
echo "Disk usage: $(du -sh $PDF_DIR)"
