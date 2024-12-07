-- PRECIPITACIÓN POR COMARCAS

-- total acumulada por año
SELECT 
    DATE(F.DS_FECHA) AS Inicio_Anio,
    SUM(DC.IND_PRECIPITACION) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA)
ORDER BY 
    Inicio_Anio;

-- media acumulada por semana
SELECT 
    DATE_ADD(F.DS_FECHA, INTERVAL(1 - DAYOFWEEK(F.DS_FECHA)) DAY) AS Inicio_Semana,
    AVG(DC.IND_PRECIPITACION) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    Inicio_Semana
ORDER BY 
    Inicio_Semana;

-- media acumulada por mes
SELECT 
    F.DS_FECHA AS Inicio_Mes,  
    AVG(DC.IND_PRECIPITACION) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
JOIN 
    LK_COMARCAS C ON M.ID_COMARCA = C.ID_COMARCA
WHERE 
    C.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA), MONTH(F.DS_FECHA)  -- Agrupamos por año y mes
ORDER BY 
    Inicio_Mes;



-- TEMPERATURA MINIMA POR COMARCAS

-- media por año (revisar el año 2020, no sale bien)
SELECT 
    DATE(F.DS_FECHA) AS Inicio_Anio,
    AVG(DC.IND_TEMP_MIN) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN    
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA)
ORDER BY 
    Inicio_Anio;


-- media por semana 
SELECT 
    DATE_ADD(F.DS_FECHA, INTERVAL(1 - DAYOFWEEK(F.DS_FECHA)) DAY) AS Inicio_Semana,
    AVG(DC.IND_TEMP_MIN) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    Inicio_Semana
ORDER BY 
    Inicio_Semana;


-- media por mes 
SELECT 
    F.DS_FECHA AS Inicio_Mes,  
    AVG(DC.IND_TEMP_MIN) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
JOIN 
    LK_COMARCAS C ON M.ID_COMARCA = C.ID_COMARCA
WHERE 
    C.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA), MONTH(F.DS_FECHA)  -- Agrupamos por año y mes
ORDER BY 
    Inicio_Mes;



-- TEMPERATURA MAXIMA POR COMARCAS

-- media por año 
SELECT 
    DATE(F.DS_FECHA) AS Inicio_Anio,
    AVG(DC.IND_TEMP_MAX) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN    
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA)
ORDER BY 
    Inicio_Anio;


-- media por semana 
SELECT 
    DATE_ADD(F.DS_FECHA, INTERVAL(1 - DAYOFWEEK(F.DS_FECHA)) DAY) AS Inicio_Semana,
    AVG(DC.IND_TEMP_MAX) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    Inicio_Semana
ORDER BY 
    Inicio_Semana;

-- media por mes 
SELECT 
    F.DS_FECHA AS Inicio_Mes,  
    AVG(DC.IND_TEMP_MAX) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
JOIN 
    LK_COMARCAS C ON M.ID_COMARCA = C.ID_COMARCA
WHERE 
    C.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA), MONTH(F.DS_FECHA)  -- Agrupamos por año y mes
ORDER BY 
    Inicio_Mes;


-- TEMPERATURA MEDIA POR COMARCAS

-- media por año 
SELECT 
    DATE(F.DS_FECHA) AS Inicio_Anio,
    AVG(DC.IND_TEMP_MED) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN    
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA)
ORDER BY 
    Inicio_Anio;


-- media por semana 
SELECT 
    DATE_ADD(F.DS_FECHA, INTERVAL(1 - DAYOFWEEK(F.DS_FECHA)) DAY) AS Inicio_Semana,
    AVG(DC.IND_TEMP_MED) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    Inicio_Semana
ORDER BY 
    Inicio_Semana;

-- media por mes 
SELECT 
    F.DS_FECHA AS Inicio_Mes,  
    AVG(DC.IND_TEMP_MED) AS ? -- Reemplazar con el nombre de la comarca
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
JOIN 
    LK_COMARCAS C ON M.ID_COMARCA = C.ID_COMARCA
WHERE 
    C.ID_COMARCA = ? -- Reemplazar con el ID de la comarca
GROUP BY 
    YEAR(F.DS_FECHA), MONTH(F.DS_FECHA)  -- Agrupamos por año y mes
ORDER BY 
    Inicio_Mes;


