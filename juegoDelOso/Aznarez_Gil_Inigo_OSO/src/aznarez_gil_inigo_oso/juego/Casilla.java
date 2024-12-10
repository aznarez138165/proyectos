/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package aznarez_gil_inigo_oso.juego;

/**
 *
 * @author Iñigo Aznárez
 */
public class Casilla {
    private boolean ocupada;
    private boolean usadoOso;
    private int i;
    private int j;
    private String c;
    
    public Casilla(boolean ocupada, boolean usadoOso, int i, int j, String c) {
        this.ocupada = ocupada;
        this.usadoOso = usadoOso;
        this.i = i;
        this.j = j;
        this.c = c;
    }

    public boolean isOcupada() {
        return ocupada;
    }

    public void setOcupada(boolean ocupada) {
        this.ocupada = ocupada;
    }

    public boolean isUsadoOso() {
        return usadoOso;
    }

    public void setUsadoOso(boolean usadoOso) {
        this.usadoOso = usadoOso;
    }

    public int getI() {
        return i;
    }

    public void setI(int i) {
        this.i = i;
    }

    public int getJ() {
        return j;
    }

    public void setJ(int j) {
        this.j = j;
    }

    public String getC() {
        return c;
    }

    public void setC(String c) {
        this.c = c;
    }    
    
}
