/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Main.java to edit this template
 */
package aznarez_gil_inigo_oso.juego;

import java.util.Scanner;

/**
 *
 * @author Iñigo Aznarez
 */
public class OsoSolitarioConsola {

    /**
     * @param args the command line arguments
     */
    public static void main(String[] args) {
        // TODO code application logic here
        
        Scanner scanner = new Scanner(System.in);
        int width = 0,height =0 ;
        do{
            System.out.print("Introduce el ancho del tablero: ");
            width = scanner.nextInt();
            if(width < 3 || width > 5)
                System.out.println("No esta permitido, tiene que ser >= 3 y <= 5");
        }while( width < 3 || width > 5);
        
        do{
            System.out.print("Introduce el alto del tablero: ");
            height = scanner.nextInt();
            if(height < 3 || height > 6)
                System.out.println("No esta permitido, tiene que ser >= 3 y <= 6");
        }while( height < 3 || height > 6);

        Tablero tablero = new Tablero(width, height);
        
        int count = 0;
        
        while(!tablero.esFinal()){
            System.out.print("Introduce las coordenadas de la casilla que quieres escribir: (x y) ");
            int x = scanner.nextInt();
            int y = scanner.nextInt();

            System.out.print("Introduce el caracter que quieres escribir: ");
            String c = scanner.next();

            tablero.colocarCaracter(x,y,c);
            
            if(tablero.esOSO(x, y)){
                System.out.println("Se ha completado un OSO");
                count++;
            }
            
            tablero.imprimirTablero();
        }
        System.out.println("Resultado final: ");
        tablero.imprimirTablero();
        System.out.println("Se han realizado "+count+" palabras OSO");
                
    }
    
}




