import os
import requests
import re
from datetime import datetime
import pandas as pd
from bs4 import BeautifulSoup

# Carpeta para guardar los datos
output_folder = "datos_climatologicos"
os.makedirs(output_folder, exist_ok=True)

# Carpeta de salida para los datos transformados
output_transformed_folder = "datos_transformados"
os.makedirs(output_transformed_folder, exist_ok=True)

# Años a descargar (últimos 18 años)
años_descargar = list(range(2006, 2023))

# Función para obtener estaciones automáticas
def obtener_estaciones_automaticas():
    url_estaciones = "https://meteo.navarra.es/estaciones/descargardatos.cfm"
    response = requests.get(url_estaciones)
    if response.status_code != 200:
        print("Error al acceder a la pagina de estaciones.")
        return []
    
    soup = BeautifulSoup(response.text, 'html.parser')
    stations = []

    for a_tag in soup.find_all('a', href=True):
        href = a_tag['href']
        if "IDEstacion" in href:
            img_tag = a_tag.find_previous('img', src=True)
            if img_tag and "estacionautomatica.gif" in img_tag['src']:
                station_id = href.split('IDEstacion=')[-1]
                station_name = a_tag.text.strip()
                stations.append((station_id, station_name))

    if not stations:
        print("No se encontraron estaciones automaticas.")
    print(f"Se encontraron {len(stations)} estaciones automaticas.\n")
    return stations

# Función para descargar los datos de las estaciones
def descargar_datos(id_estacion, nombre_estacion, identificador):
    url = f"http://meteo.navarra.es/estaciones/descargardatos_estacion.cfm?IDEstacion={id_estacion}"
    response = requests.get(url)
    if response.status_code != 200:
        print(f"Error al acceder a la pagina de la estacion {id_estacion}")
        return False
    
    enlaces_csv = re.findall(r"d\.add\(\d+,\d+,'[^']+','([^']+\.csv)'\)", response.text)
    if not enlaces_csv:
        print(f"No se encontraron archivos .csv para la estacion {id_estacion}")
        return False

    años_encontrados = {
        int(re.search(r"_(\d{4})\.csv", enlace).group(1)) 
        for enlace in enlaces_csv if re.search(r"_(\d{4})\.csv", enlace)
    }

    print(f"Estacion {id_estacion}: {nombre_estacion}")
    años_filtrados = años_encontrados.intersection(años_descargar)
    print(f"Años filtrados: {años_filtrados}")

    if años_filtrados != set(años_descargar):
        print(f"La estacion no tiene todos los años requeridos. Omitiendo descarga.\n")
        return False

    for enlace in enlaces_csv:
        año = int(re.search(r"_(\d{4})\.csv", enlace).group(1))
        if 2006 <= año <= 2023:
            archivo_url = f"http://meteo.navarra.es{enlace}"
            nombre_archivo_original = os.path.basename(archivo_url)
            nombre_archivo_identificado = f"{identificador}_{nombre_archivo_original}"
            print(f"Descargando: {nombre_archivo_identificado}")
            archivo_response = requests.get(archivo_url)
            if archivo_response.status_code == 200:
                with open(os.path.join(output_folder, nombre_archivo_identificado), 'wb') as f:
                    f.write(archivo_response.content)
            else:
                print(f"Error al descargar {nombre_archivo_identificado} para la estacion {id_estacion}")
    
    print(f"Descarga completada para la estacion {id_estacion}\n")
    return True

# Función para transformar la fecha
def transformar_fecha(original_datetime):
    if len(original_datetime.split(' ')) == 1:
        original_datetime += " 00:00:00"
    try:
        formatted_datetime = pd.to_datetime(original_datetime, format="%Y/%m/%d %H:%M:%S")
    except ValueError:
        try:
            formatted_datetime = datetime.strptime(original_datetime, "%d/%m/%Y %H:%M:%S")
        except ValueError:
            raise ValueError(f"El formato de la fecha no es valido: {original_datetime}")
    return formatted_datetime.strftime("%y/%m/%d")

# Función para transformar los caracteres especiales
def transformar_caracteres_especiales(header):
    reemplazos = {
        'á': 'a',   
        'í': 'i',   
        'ó': 'o',  
        'º': '0',    
        '�': '0',  
        '²': '2',   
    }

    header = [reemplazar_caracteres(c, reemplazos) for c in header]
    return header

# Función para reemplazar caracteres
def reemplazar_caracteres(cadena, reemplazos):
    for antiguo, nuevo in reemplazos.items():
        cadena = cadena.replace(antiguo, nuevo)
    return cadena
    

# Función para procesar los archivos CSV
def transformar_datos():
    csv_files = [file for file in os.listdir(output_folder) if file.endswith(".csv")]
    for file in csv_files:
        input_path = os.path.join(output_folder, file)
        output_path = os.path.join(output_transformed_folder, file)

        with open(input_path, "r", encoding="ISO-8859-1") as infile:
            lines = infile.readlines()

        # Reemplazar ',' por '.' en todas las líneas y luego reemplazar ';' por ','
        modified_lines = [line.replace(",", ".") for line in lines]
        modified_lines2 = [line.replace(";", ",") for line in modified_lines]

        header = modified_lines2[0]
        header = transformar_caracteres_especiales(header) 
        header = ''.join(header)  
        header = [header.rstrip(",\n")]  
        header = ''.join(header)  
        header += "\n"

        data_lines = []
        for line in modified_lines2[1:]:
            fields = line.strip().split(",")
            if len(fields) > 0:
                original_datetime = fields[0]
                fields[0] = transformar_fecha(original_datetime)
                data_lines.append(",".join(fields) + "\n")

        with open(output_path, "w", encoding="ISO-8859-1") as outfile:
            outfile.write(header)
            outfile.writelines(data_lines)

        print(f"Archivo procesado: {file} -> {output_path}")


# Función principal que integra todo
def ejecutar_proceso():
    estaciones_automaticas = obtener_estaciones_automaticas()
    contador_identificador = 1

    for id_estacion, nombre_estacion in estaciones_automaticas:
        print(f"Procesando estacion: {nombre_estacion} (ID: {id_estacion})")
        if descargar_datos(id_estacion, nombre_estacion, identificador=contador_identificador):
            contador_identificador += 1

    transformar_datos()
    

# Ejecutar el proceso
if __name__ == "__main__":
    ejecutar_proceso()