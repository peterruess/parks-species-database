SELECT DISTINCT sp.common_name, p.park_name, p.street, p.borough , p.info_url
FROM parks p
INNER JOIN sightings si USING(park_id)
INNER JOIN species sp USING(genus_name, species_name)
WHERE p.park_id = "cen_par_M"
ORDER BY sp.common_name;
