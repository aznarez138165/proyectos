CREATE TABLE elementos_dragdrop (
    id_elemento INT AUTO_INCREMENT PRIMARY KEY,    -- Identificador único para cada elemento
    pregunta_id INT NOT NULL,                      -- Clave foránea que referencia la pregunta
    texto_elemento TEXT NOT NULL,                  -- El texto o contenido del elemento
    posicion_correcta INT NOT NULL,                -- La posición correcta del elemento en el drag & drop
    FOREIGN KEY (pregunta_id) REFERENCES preguntas_dragdrop(id_pregunta)  -- Relación con la tabla preguntas
);

