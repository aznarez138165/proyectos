/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package aznarez_gil_inigo_oso.juego;


/**
 *
 * @author Iñigo Aznarez
 */
public class Tablero {
    private int cols;
    private int rows;
    private Casilla tablero [][];

    public Tablero(int rows, int cols) {
        this.cols = cols;
        this.rows = rows;
        this.tablero = new Casilla[rows][cols];
        inicializarTablero();
    }

    public int getCols() {
        return cols;
    }

    public void setCols(int cols) {
        this.cols = cols;
    }

    public int getRows() {
        return rows;
    }

    public void setRows(int rows) {
        this.rows = rows;
    }

    public Casilla[][] getTablero() {
        return tablero;
    }

    public void setTablero(Casilla[][] tablero) {
        this.tablero = tablero;
    }

    // Método para colocar un caracter en una posición específica del tablero
    public void colocarCaracter(int x, int y, String c) {
        c = c.toUpperCase();
        if(!c.equals("O") && !c.equals("S")){
            System.out.println("El caracter no es valido");
            return;
        }
            
        if (x < 0 || x > rows-1 || y < 0 || y > cols-1) {
            System.out.println("Posicion fuera de los limites del tablero x="+x+",y="+y);
        }
        else{
            if(!tablero[x][y].isOcupada()){ //casilla vacía, se puede escribir
                tablero[x][y].setC(c);
                tablero[x][y].setOcupada(true);
            }else{//casilla ocupada, no se puede escribir
                System.out.println("Posicion ocupada.");
            }
        }
    }
    
    // Método para obtener el caracter en una posición específica del tablero
    public String obtenerCaracter(int x, int y) {
        if (x >= 0 && x <= cols-1 && y >= 0 && y <= rows-1) {
            return tablero[x][y].getC();
        } else {
            System.out.println("Posicion fuera de los limites del tablero. x="+x+",y="+y);
            return "\0"; // Caracter nulo
        }
    }
    
    // Método para inicializar todas las casillas del tablero con caracteres vacíos
    private void inicializarTablero() {
        for (int i = 0; i < rows ; i++) {
            for (int j = 0; j < cols; j++) {
                tablero[i][j] = new Casilla(false, false, i, j, " ");
            }
        }
    }
    
    // Método para imprimir el tablero en la consola
    public void imprimirTablero() {
        for (int i = 0; i < rows; i++) {
            for (int j = 0; j <  cols; j++) {
                System.out.print(tablero[i][j].getC() + " ");
            }
            System.out.println();
        }
    }
    
    public boolean esFinal() {
        for (int i = 0; i < rows; i++) {
            for (int j = 0; j < cols; j++) {
                if (!tablero[i][j].isOcupada()) {
                    return false;
                }
            }
        }
        return true;
    }
    
    public boolean esOSO(int i, int j) {
        Casilla casilla = tablero[i][j];
        String c = casilla.getC();
        if(c.equals("S")){
            //Oso en horizontal   
            if(j-1 >= 0 && j+1 <= cols - 1 && tablero[i][j-1].getC().equals("O") && tablero[i][j+1].getC().equals("O")){
                if (casilla.isUsadoOso() || tablero[i][j - 1].isUsadoOso() || tablero[i][j + 1].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i][j - 1].setUsadoOso(true);
                tablero[i][j + 1].setUsadoOso(true);

//                System.out.println("Oso en horizontal, posiciones de las letras: ");
//                System.out.println("S: (" + i + ", " + j + ")");
//                System.out.println("O1: (" + i + ", " + (j - 1) + ")");
//                System.out.println("O2: (" + i + ", " + (j + 1) + ")");
                return true;
            }
            //Oso en vertical
            else if(i-1 >= 0 && i+1 <= rows - 1 && tablero[i-1][j].getC().equals("O") && tablero[i+1][j].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i+1][j].isUsadoOso() || tablero[i-1][j].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i-1][j].setUsadoOso(true);
                tablero[i+1][j].setUsadoOso(true);
                
//                System.out.println("Oso en vertical, posiciones de las letras: ");
//                System.out.println("S: (" + i + ", " + j + ")");
//                System.out.println("O1: (" + (i - 1) + ", " + j + ")");
//                System.out.println("O2: (" + (i + 1) + ", " + j + ")");
                return true;
            }
            
            //Oso en diagonal derecha
            else if(i-1 >= 0 && i+1 <= rows - 1 && j-1 >= 0 && j+1 <= cols - 1 &&  tablero[i-1][j-1].getC().equals("O") && tablero[i+1][j+1].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i-1][j-1].isUsadoOso() || tablero[i+1][j+1].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i-1][j-1].setUsadoOso(true);
                tablero[i+1][j+1].setUsadoOso(true);
                
//                System.out.println("Oso en diagonal derecha, posiciones de las letras: ");
//                System.out.println("S: (" + i + ", " + j + ")");
//                System.out.println("O1: (" + (i - 1) + ", " + (j - 1) + ")");
//                System.out.println("O2: (" + (i + 1) + ", " + (j + 1) + ")");
                return true;
            }
            
            //Oso en diagonal izquierda
            else if(i-1 >= 0 && i+1 <= rows - 1 && j-1 >= 0 && j+1 <= cols - 1 && tablero[i-1][j+1].getC().equals("O") && tablero[i+1][j-1].getC().equals("O")){        
                if(casilla.isUsadoOso() || tablero[i-1][j+1].isUsadoOso() || tablero[i+1][j-1].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i-1][j+1].setUsadoOso(true);
                tablero[i+1][j-1].setUsadoOso(true);
                
//                System.out.println("Oso en diagonal izquierda, posiciones de las letras: ");
//                System.out.println("S: (" + i + ", " + j + ")");
//                System.out.println("O1: (" + (i - 1) + ", " + (j + 1) + ")");
//                System.out.println("O2: (" + (i + 1) + ", " + (j - 1) + ")");
                return true;
            }
            return false;
            
        }else if(c.equals("O")){  
            //OSO a la derecha horizontal
            if(j + 2 <= cols-1 && tablero[i][j+1].getC().equals("S") && tablero[i][j+2].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i][j+1].isUsadoOso() || tablero[i][j+2].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i][j+1].setUsadoOso(true);
                tablero[1][j+2].setUsadoOso(true);
                
//                System.out.println("OSO a la derecha en horizontal, posiciones de las letras: ");
//                System.out.println("O1: (" + i + ", " + j + ")");
//                System.out.println("S: (" + i + ", " + (j + 1) + ")");
//                System.out.println("O2: (" + i + ", " + (j + 2) + ")");
                return true;
            }
            
            //Oso a la izquierda horizontal
            else if(j - 2 >= 0 && tablero[i][j-1].getC().equals("S") && tablero[i][j-2].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i][j-2].isUsadoOso() || tablero[i][j-1].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i][j-2].setUsadoOso(true);
                tablero[i][j-1].setUsadoOso(true);
                
//                System.out.println("OSO a la izquierda en horizontal, posiciones de las letras: ");
//                System.out.println("O1: (" + i + ", " + (j - 2) + ")");
//                System.out.println("S: (" + i + ", " + (j - 1) + ")");
//                System.out.println("O2: (" + i + ", " + j + ")");
                return true;
            }

            //OSO a abajo en vertical
            else if(i + 2 <= rows-1 && tablero[i+1][j].getC().equals("S") && tablero[i+2][j].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i+1][j].isUsadoOso() || tablero[i+2][j].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i+1][j].setUsadoOso(true);
                tablero[i+2][j].setUsadoOso(true);
                
//                System.out.println("OSO a abajo en vertical, posiciones de las letras: ");
//                System.out.println("O1: (" + i + ", " + j + ")");
//                System.out.println("S: (" + (i + 1) + ", " + j + ")");
//                System.out.println("O2: (" + (i + 2) + ", " + j + ")");
                return true;
            }
            
            //OSO a arriba en vertical
            else if(i - 2 >= 0 && tablero[i-1][j].getC().equals("S") && tablero[i-2][j].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i-1][j].isUsadoOso() || tablero[i-2][j].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i-2][j].setUsadoOso(true);
                tablero[i-1][j].setUsadoOso(true);
                
//                System.out.println("OSO a arriba en vertical, posiciones de las letras: ");
//                System.out.println("O1: (" + (i - 2) + ", " + j + ")");
//                System.out.println("S: (" + (i - 1) + ", " + j + ")");
//                System.out.println("O2: (" + i + ", " + j + ")");
                return true;
            }
            
            //OSO en diagonal derecha abajo
            else if(i + 2 <= rows-1 && j + 2 <= cols-1 && tablero[i+1][j+1].getC().equals("S") && tablero[i+2][j+2].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i+1][j+1].isUsadoOso() || tablero[i+2][j+2].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i+2][j+2].setUsadoOso(true);
                tablero[i+1][j+1].setUsadoOso(true);
                
//                System.out.println("OSO en diagonal derecha abajo, posiciones de las letras: ");
//                System.out.println("O1: (" + i + ", " + j + ")");
//                System.out.println("S: (" + (i + 1) + ", " + (j + 1) + ")");
//                System.out.println("O2: (" + (i + 2) + ", " + (j + 2) + ")");
                return true;
            }
            
            //OSO en diagonal derecha arriba
            else if(i - 2 >= 0 && j - 2 >= 0 && tablero[i-1][j-1].getC().equals("S") && tablero[i-2][j-2].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i-1][j-1].isUsadoOso() || tablero[i-2][j-2].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i-1][j-1].setUsadoOso(true);
                tablero[i-2][j-2].setUsadoOso(true);
                
//                System.out.println("OSO a arriba en vertical, posiciones de las letras: ");
//                System.out.println("O1: (" + (i - 2) + ", " + (j - 2) + ")");
//                System.out.println("S: (" + (i - 1) + ", " + (j - 1) + ")");
//                System.out.println("O2: (" + i + ", " + j + ")");
                return true;
            }
            
            //OSO en diagonal izquierda abajo
            else if(i + 2 <= rows-1 && j - 2 >= 0 && tablero[i+1][j-1].getC().equals("S") && tablero[i+2][j-2].getC().equals("O")){
                if(casilla.isUsadoOso() || tablero[i+1][j-1].isUsadoOso() || tablero[i+2][j-2].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i+2][j-2].setUsadoOso(true);
                tablero[i+1][j-1].setUsadoOso(true);
                
//                System.out.println("OSO a arriba en vertical, posiciones de las letras: ");
//                System.out.println("O1: (" + i + ", " + j + ")");
//                System.out.println("S: (" + (i + 1) + ", " + (j - 1) + ")");
                System.out.println("O2: (" + (i + 2) + ", " + (j - 2) + ")");
                return true;
            }
            
            
            //OSO en diagonal izquierda arriba
            else if(i - 2 >= 0 && j + 2 <= cols-1 && tablero[i-1][j+1].getC().equals("S") && tablero[i-2][j+2].getC().equals("O")){
               if(casilla.isUsadoOso() || tablero[i-1][j+1].isUsadoOso() || tablero[i-2][j+2].isUsadoOso()) return false;
                casilla.setUsadoOso(true);
                tablero[i-1][j+1].setUsadoOso(true);
                tablero[i-2][j+2].setUsadoOso(true);
                
//                System.out.println("OSO a arriba en vertical, posiciones de las letras: ");
//                System.out.println("O1: (" + (i - 2) + ", " + (j + 2) + ")");
//                System.out.println("S: (" + (i - 1) + ", " + (j + 1) + ")");
//                System.out.println("O2: (" + i + ", " + j + ")");
                return true;
            }

            return false;
        }
        return false;
    }
    
}