"""Build the small, static SVG geometry used by the public working-area map.

Source: BIG, Wilayah Administrasi Kabupaten/Kota FeatureServer layer 0.
Run with Python 3; no third-party packages are required.
"""

import json
import urllib.parse
import urllib.request
from pathlib import Path


SOURCE = (
    "https://geoservices.big.go.id/rbi/rest/services/Hosted/"
    "Wilayah_Administrasi_Kabupaten__Kota/FeatureServer/0/query"
)
ROOT = Path(__file__).resolve().parents[1]
OUTPUT = ROOT / "resources/data/wilayah-map.json"
WEST, EAST, NORTH, SOUTH = 111.23, 112.54, -7.42, -8.42
WIDTH, HEIGHT = 640, 490
ACTIVE = {"Trenggalek": "trenggalek", "Tulungagung": "tulungagung", "Kediri": "kediri"}


def project(point):
    longitude, latitude = point[:2]
    x = (longitude - WEST) / (EAST - WEST) * WIDTH
    y = (NORTH - latitude) / (NORTH - SOUTH) * HEIGHT
    return f"{x:.1f} {y:.1f}"


def path_for(geometry):
    polygons = geometry["coordinates"]
    if geometry["type"] == "Polygon":
        polygons = [polygons]
    return " ".join(
        "M" + " L".join(project(point) for point in ring) + " Z"
        for polygon in polygons
        for ring in polygon
        if len(ring) > 2
    )


params = urllib.parse.urlencode(
    {
        "where": "wadmpr = 'Jawa Timur'",
        "geometry": f"{WEST},{SOUTH - .1},{EAST},{NORTH}",
        "geometryType": "esriGeometryEnvelope",
        "inSR": "4326",
        "spatialRel": "esriSpatialRelIntersects",
        "outFields": "wadmkk,tipadm",
        "returnGeometry": "true",
        "outSR": "4326",
        "geometryPrecision": "5",
        "maxAllowableOffset": "0.002",
        "f": "geojson",
    }
).encode()
with urllib.request.urlopen(urllib.request.Request(SOURCE, data=params), timeout=60) as response:
    features = json.load(response)["features"]

regions = {}
context = []
city = None
for feature in features:
    name = feature["properties"]["wadmkk"]
    path = path_for(feature["geometry"])
    if name in ACTIVE:
        regions[ACTIVE[name]] = path
    elif name == "Kota Kediri":
        city = path
    else:
        context.append(path)

if set(regions) != set(ACTIVE.values()) or not city:
    raise RuntimeError("BIG response did not contain all working-area boundaries")

output = {
    "source": SOURCE.rsplit("/query", 1)[0],
    "viewBox": f"0 0 {WIDTH} {HEIGHT}",
    "context": context,
    "regions": {slug: regions[slug] for slug in ("trenggalek", "tulungagung", "kediri")},
    "kediriCity": city,
}
OUTPUT.write_text(json.dumps(output, ensure_ascii=False, separators=(",", ":")) + "\n", encoding="utf-8")
print(f"Wrote {OUTPUT.relative_to(ROOT)} with {len(context)} surrounding areas")
