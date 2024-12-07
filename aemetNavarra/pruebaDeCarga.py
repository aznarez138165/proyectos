import time
import pymysql
from concurrent.futures import ThreadPoolExecutor

# Configuración de la base de datos
db_config = {
    'host': 'eim-srv-mysql.lab.unavarra.es',
    'user': 'aaee_07',
    'password': 'aaee_07',
    'database': 'aaee_07'
}

# Consulta SQL
query = """
SELECT 
    YEAR(F.DS_FECHA) AS Anio,
    SUM(DC.IND_PRECIPITACION) AS PrecipitacionTotal
FROM 
    DT_DATOS_CLIMATICOS DC
JOIN 
    LK_FECHAS F ON DC.ID_FECHA = F.ID_FECHA
JOIN 
    LK_MUNICIPIOS M ON DC.ID_MUNICIPIO = M.ID_MUNICIPIO
WHERE 
    M.ID_COMARCA = 1
GROUP BY 
    Anio
ORDER BY 
    Anio;
"""

# Función para ejecutar una consulta
def ejecutar_consulta():
    try:
        conn = pymysql.connect(**db_config)
        with conn.cursor() as cursor:
            start_time = time.time()
            cursor.execute(query)
            result = cursor.fetchall()
            end_time = time.time()
            execution_time = end_time - start_time
            return execution_time, True  # Éxito
    except Exception as e:
        print(f"Error: {e}")
        return None, False  # Fallo
    finally:
        conn.close()

# Simulación de usuarios concurrentes
def prueba_carga(usuarios):
    tiempos_ejecucion = []
    exitosas = 0
    intentos = usuarios  # Número total de consultas intentadas

    with ThreadPoolExecutor(max_workers=usuarios) as executor:
        futures = [executor.submit(ejecutar_consulta) for _ in range(usuarios)]
        for future in futures:
            tiempo, exito = future.result()
            if exito:
                exitosas += 1
                if tiempo is not None:
                    tiempos_ejecucion.append(tiempo)

    # Calcular tiempo promedio de ejecución
    tiempo_promedio = sum(tiempos_ejecucion) / len(tiempos_ejecucion) if tiempos_ejecucion else 0
    tasa_exito = (exitosas / intentos) * 100  # Calcular tasa de éxito
    return tiempo_promedio, len(tiempos_ejecucion), tasa_exito

# Prueba con diferentes números de usuarios
if __name__ == "__main__":
    resultados = []
    for usuarios in [10, 50, 100]:
        print(f"\nPrueba con {usuarios} usuarios concurrentes:")
        start_time = time.time()
        tiempo_promedio, total_consultas, tasa_exito = prueba_carga(usuarios)
        end_time = time.time()
        tiempo_total = end_time - start_time

        # Calcular consultas por segundo
        consultas_por_segundo = total_consultas / tiempo_total if tiempo_total > 0 else 0

        print(f"Tiempo total para {usuarios} usuarios: {tiempo_total:.2f} segundos")
        print(f"Consultas por segundo: {consultas_por_segundo:.2f} consultas/s")
        print(f"Tasa de éxito: {tasa_exito:.2f}%\n")

        # Registrar resultados
        resultados.append({
            'Usuarios': usuarios,
            'Tiempo Promedio': tiempo_promedio,
            'Tiempo Total': tiempo_total,
            'Consultas por Segundo': consultas_por_segundo,
            'Tasa de Éxito': tasa_exito
        })

    # Mostrar resumen de resultados
    print("\nResumen de resultados:")
    for resultado in resultados:
        print(f"Usuarios: {resultado['Usuarios']}, "
              f"Tiempo Promedio: {resultado['Tiempo Promedio']:.4f} s, "
              f"Tiempo Total: {resultado['Tiempo Total']:.2f} s, "
              f"Consultas por Segundo: {resultado['Consultas por Segundo']:.2f} consultas/s, "
              f"Tasa de Éxito: {resultado['Tasa de Éxito']:.2f}%")
