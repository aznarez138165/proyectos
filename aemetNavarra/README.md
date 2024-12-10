# Proyecto Final de Análisis de Aplicaciones Empresariales  
**Autor**: Iñigo Aznárez Gil  

## Descripción General  
Este proyecto se centra en el análisis y procesamiento de datos empresariales mediante un flujo ETL (Extracción, Transformación y Carga) y la construcción de un Data Warehouse. A través de diversos scripts, se gestiona todo el ciclo de vida de los datos, desde su descarga y transformación hasta su inserción en un sistema de almacenamiento estructurado.

## Ejecución del Proyecto  
El proyecto está diseñado para ser ejecutado de forma eficiente mediante scripts automatizados. A continuación, se detalla el procedimiento:

### Scripts Principales  
1. **`descargar_datos_transformar.py`**  
   Realiza la descarga de los datos iniciales desde las fuentes correspondientes y lleva a cabo las transformaciones necesarias para su normalización.
   
2. **`comprobar_csv.py`**  
   Valida los archivos CSV generados para garantizar la calidad y consistencia de los datos antes de su inserción en las tablas.

3. **`crear_tablas_insertar.py`**  
   Crea las tablas en la base de datos y realiza la carga de datos desde los archivos procesados en formato CSV.

### Ejecución Completa  
Para ejecutar todo el flujo de trabajo de manera automatizada, existen dos opciones:

- **Usando permisos de ejecución**:  
  ./ejecutarTodo.sh

- **Usando un script bash estándar**:  
    bash ejecutarTodo.sh

### Documentación
La documentación detallada del proyecto se encuentra en la carpeta documentacion. Los archivos incluidos proporcionan una guía completa sobre el diseño y desarrollo del proyecto.

1. **`procesoETL.pdf`**  
   Descripción del flujo ETL y su implementación en el proyecto.
   
2. **`dataWareHouse.pdf`**  
   Descripción del Data Warehouse y su estructura en la base de datos.

3. **`consultas.pdf`**  
   Descripción de las consultas y sus resultados obtenidos.
