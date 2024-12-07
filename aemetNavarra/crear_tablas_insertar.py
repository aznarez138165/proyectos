import pymysql
import os
import calendar
from datetime import datetime
import csv


# Variables generales de conexión a la base de datos
server = 'eim-srv-mysql.lab.unavarra.es'
user = 'aaee_07'
password = 'aaee_07'
bd = 'aaee_07'

# Define las claves de los campos que deseas extraer
fields_of_interest = {
    "temperatura_maxima": "Temperatura maxima 0C",
    "temperatura_media": "Temperatura media 0C",
    "temperatura_minima": "Temperatura minima 0C",
    "precipitacion_acumulada": "Precipitacion acumulada l/m2"
}

# Función para conectar a la base de datos
def conectar_bd():
    try:
        print("Conectando a la base de datos...")
        conn = pymysql.connect(
            host=server,
            user=user,
            password=password,
            database=bd,
            charset='utf8',
            cursorclass=pymysql.cursors.DictCursor
        )
        print("Conexión establecida.")
        return conn
    except pymysql.MySQLError as e:
        print(f"Error al conectar a la base de datos: {e}")
        exit()

# Función para vaciar tablas
def vaciar_tablas(conn):
    c = conn.cursor()
    print("Vaciando tablas...")
    c.execute("SET FOREIGN_KEY_CHECKS = 0;") 
    c.execute("TRUNCATE TABLE DT_DATOS_CLIMATICOS")
    c.execute("TRUNCATE TABLE LK_MUNICIPIOS")
    c.execute("TRUNCATE TABLE LK_COMARCAS")
    c.execute("TRUNCATE TABLE LK_FECHAS")
    c.execute("SET FOREIGN_KEY_CHECKS = 1;")  
    print("Tablas vaciadas.")
    conn.commit()
    c.close()

# Función para crear las tablas
def crear_tablas(conn):
    c = conn.cursor()
    print("Creando tablas...")

    c.execute("DROP TABLE IF EXISTS DT_DATOS_CLIMATICOS")
    c.execute("DROP TABLE IF EXISTS LK_MUNICIPIOS")
    c.execute("DROP TABLE IF EXISTS LK_FECHAS")
    c.execute("DROP TABLE IF EXISTS LK_COMARCAS")


    c.execute("""CREATE TABLE LK_COMARCAS (
              ID_COMARCA INT PRIMARY KEY, 
              DS_COMARCA VARCHAR(255)
              )""")
    c.execute("""CREATE TABLE LK_MUNICIPIOS (
              ID_MUNICIPIO INT PRIMARY KEY, 
              DS_MUNICIPIO VARCHAR(255), 
              ID_COMARCA INT NOT NULL, 
              FOREIGN KEY (ID_COMARCA) REFERENCES LK_COMARCAS (ID_COMARCA) ON DELETE CASCADE
              )""")
    c.execute("""CREATE TABLE LK_FECHAS (
              ID_FECHA INT PRIMARY KEY, 
              DS_FECHA DATE NOT NULL, 
              IND_DIA INT NOT NULL, 
              IND_MES INT NOT NULL, 
              IND_ANIO INT NOT NULL
              )""")
    c.execute("""CREATE TABLE DT_DATOS_CLIMATICOS (
            ID_MUNICIPIO INT NOT NULL, 
            ID_FECHA INT NOT NULL, 
            IND_TEMP_MAX DECIMAL(5,2), 
            IND_TEMP_MED DECIMAL(5,2), 
            IND_TEMP_MIN DECIMAL(5,2), 
            IND_PRECIPITACION DECIMAL(7,2), 
            CONSTRAINT FK_FECHA FOREIGN KEY (ID_FECHA) REFERENCES LK_FECHAS(ID_FECHA) ON DELETE CASCADE,
            CONSTRAINT FK_MUNICIPIO FOREIGN KEY (ID_MUNICIPIO) REFERENCES LK_MUNICIPIOS(ID_MUNICIPIO) ON DELETE CASCADE
            )""")
    conn.commit()
    print("Tablas creadas.")

# Función para insertar comarcas
def insertar_comarcas(c):
    comarcas = [
        (1, 'Tierra Estella'),
        (2, 'Zona Noroeste'),
        (3, 'Zona Pirineo'),
        (4, 'Pamplona'),
        (5, 'Navarra Media Oriental'),
        (6, 'Rivera Alta'),
        (7, 'Tudela'),
    ]
    print("Insertando comarcas...")
    for id_comarca, ds_comarca in comarcas:
        c.execute("INSERT INTO LK_COMARCAS (ID_COMARCA, DS_COMARCA) VALUES (%s, %s)", (id_comarca, ds_comarca))
        print(f"Comarca {id_comarca} : {ds_comarca} insertado")
    print("Comarcas insertadas.")


# Función para insertar municipios
def insertar_municipios(c):
    municipios = [
        (3, 'Aguilar de Codés', 1), 
        (14, 'Bargota', 1), 
        (23, 'Estella', 1), 
        (31, 'Los Arcos', 1), 
        (35, 'Ancín', 1), 
        (45, 'Trinidad de Iturgoyen', 1), 
        (49, 'Villanueva de Yerri', 1),

        (6, 'Aralar', 2), 
        (21, 'Doneztebe-Santesteban', 2), 
        (24, 'Etxarri-Aranatz', 2),
        (28, 'Gorramendi', 2), 
        (37, 'Oskotz', 2),

        (29, 'Irabia', 3),

        (5, 'Aoiz', 4), 
        (7, 'Arangoiti', 4), 
        (8, 'Arazuri', 4), 
        (15, 'Beortegui', 4), 
        (22, 'El Perdón', 4), 
        (32, 'Lumbier', 4),

        (2, 'Adios', 5), 
        (4, 'Aibar', 5), 
        (9, 'Artajona', 5), 
        (18, 'Carrascal', 5),
        (36, 'Olite', 5), 
        (39, 'San Martín de Unx', 5), 
        (43, 'Tafalla', 5), 
        (48, 'Ujué', 5), 
        (50, 'Yesa', 5),

        (25, 'Falces', 6), 
        (27, 'Funes', 6), 
        (30, 'Lerín', 6), 
        (33, 'Miranda de Arga', 6), 
        (38, 'San Adrián', 6), 
        (40, 'Sartaguda', 6), 
        (41, 'Sartaguda', 6),
        (42, 'Sesma', 6),

        (1, 'Ablitas', 7),
        (10, 'Bardenas (Barranco)', 7), 
        (11, 'Bardenas (El Plano)', 7),
        (12, 'Bardenas (El Yugo)', 7), 
        (13, 'Bardenas (Loma Negra)', 7),
        (16, 'Cadreita', 7), 
        (17, 'Carcastillo', 7), 
        (19, 'Cascante', 7),
        (20, 'Corella', 7), 
        (26, 'Fitero', 7),
        (34, 'Murillo el Fruto', 7), 
        (44, 'Traibuenas', 7),
        (46, 'Tudela (Montes del Cierzo)', 7), 
        (47, 'Tudela (Valdetellas)', 7),
    ]
    print("Insertando municipios...")
    for id_municipio, ds_municipio, id_comarca in municipios:
        c.execute("INSERT INTO LK_MUNICIPIOS (ID_MUNICIPIO, DS_MUNICIPIO, ID_COMARCA) VALUES (%s, %s, %s)", 
                  (id_municipio, ds_municipio, id_comarca))
        print(f"Municipio {id_municipio} : {ds_municipio} insertado")
    print("Municipios insertados.")


# Función para insertar fechas
def insertar_fechas(c):
    print("Insertando fechas...")
    for year in range(2006, 2024):
        for month in range(1, 13):
            days_in_month = calendar.monthrange(year, month)[1]
            for day in range(1, days_in_month + 1):
                try:
                    id_fecha = year * 10000 + month * 100 + day
                    ds_fecha = f"{year}-{month:02d}-{day:02d}"
                    c.execute("""INSERT INTO LK_FECHAS (ID_FECHA, DS_FECHA, IND_DIA, IND_MES, IND_ANIO)
                                 VALUES (%s, %s, %s, %s, %s)""", (id_fecha, ds_fecha, day, month, year))
                except ValueError:
                    pass
    print("Fechas insertadas.")

# Función para transformar la fecha al formato "YYYY-MM-DD"
def transformar_fecha(fecha):
    try:
        fecha_obj = datetime.strptime(fecha, "%y/%m/%d")
        return fecha_obj.day, fecha_obj.month, fecha_obj.year
    except ValueError:
        raise ValueError(f"El formato de la fecha no es válido: {fecha}")
    
fields_of_interest = {
    "fecha": "Fecha-hora",
    "temperatura_maxima": "Temperatura maxima 0C",
    "temperatura_media": "Temperatura media 0C",
    "temperatura_minima": "Temperatura minima 0C",
    "precipitacion_acumulada": "Precipitacion acumulada l/m2"
}

# Función para procesar archivos CSV de datos climáticos
def procesar_csvs(c, folder):
    csv_files = [os.path.join(folder, file) for file in os.listdir(folder) if file.endswith(".csv")]

    for csv_file in csv_files:
        print(f"Procesando archivo: {csv_file}")
        id_municipio = os.path.basename(csv_file).split('_')[0]

        with open(csv_file, mode='r', encoding='ISO-8859-1') as file:
            reader = csv.reader(file)
            header = next(reader) 

           
            column_indices = {field: header.index(name) for field, name in fields_of_interest.items() if name in header}
        
            for row in reader:
                fecha_csv = row[column_indices['fecha']] if row[column_indices['fecha']] else None
                dia, mes, anio = transformar_fecha(fecha_csv)
                id_fecha = anio * 10000 + mes * 100 + dia
                
                try:
                    temp_max = float(row[column_indices['temperatura_maxima']]) if row[column_indices['temperatura_maxima']] else None

                    temp_med = None 
                    if 'temperatura_media' in column_indices:
                        temp_med = float(row[column_indices['temperatura_media']]) if row[column_indices['temperatura_media']] else None
                    temp_min = float(row[column_indices['temperatura_minima']]) if row[column_indices['temperatura_minima']] else None
                    precipitacion = float(row[column_indices['precipitacion_acumulada']]) if row[column_indices['precipitacion_acumulada']] else None
                    insertar_datos_climaticos(c, id_municipio, id_fecha, temp_max, temp_med, temp_min, precipitacion)
                except ValueError as e:
                    print(f"Error procesando fila {row}: {e}")
                    continue
                except IndexError:
                    print(f"Fila incompleta en archivo {csv_file}: {row}")
                    continue

    print("Datos climáticos insertados.")

# Función para verificar si el municipio existe en la base de datos
def municipio_existe(c, id_municipio):
    c.execute("SELECT COUNT(*) FROM LK_MUNICIPIOS WHERE ID_MUNICIPIO = %s", (id_municipio,))
    count = c.fetchone()['COUNT(*)']
    return count > 0

    
# Función para insertar datos climáticos
def insertar_datos_climaticos(c, id_municipio, id_fecha, temp_max, temp_med, temp_min, precipitacion):
    if not municipio_existe(c, id_municipio):
        print(f"Municipio {id_municipio} no existe en LK_MUNICIPIOS. No se puede insertar los datos climáticos.")
        return
    try:
        c.execute("""INSERT INTO DT_DATOS_CLIMATICOS 
                     (ID_MUNICIPIO, ID_FECHA, IND_TEMP_MAX, IND_TEMP_MED, IND_TEMP_MIN, IND_PRECIPITACION) 
                     VALUES (%s, %s, %s, %s, %s, %s)""",
                  (id_municipio, id_fecha, temp_max, temp_med, temp_min, precipitacion))
    except pymysql.MySQLError as e:
        print(f"Error al insertar datos climáticos: {e}")

# Función principal para ejecutar todo el proceso
def main():
    conn = conectar_bd()
    vaciar_tablas(conn)
    crear_tablas(conn)
    c = conn.cursor()
    insertar_comarcas(c)
    conn.commit()
    insertar_municipios(c)
    conn.commit()
    insertar_fechas(c)
    conn.commit()
    procesar_csvs(c, "datos_transformados")
    conn.commit()
    conn.close()
    c.close()
    print("Proceso completo.")

# Ejecutar el script
if __name__ == "__main__":
    main()
