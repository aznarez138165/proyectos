/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Classes/Class.java to edit this template
 */
package aznarez_gil_inigo_oso.juego_completo;


import aznarez_gil_inigo_oso.juego.Juego;
import aznarez_gil_inigo_oso.net.ClienteHandler;
import java.io.DataInputStream;
import java.io.DataOutputStream;
import java.io.IOException;
import java.net.ServerSocket;
import java.net.Socket;
import java.util.HashMap;
import java.util.LinkedList;
import java.util.Map;
import java.util.Queue;
import java.util.Scanner;

/**
 *
 * @author Iñigo Aznárez
 */
public class ServidorJuego {
    private static int filas = 0;
    private static int columnas = 0;
    private final int port;
    private static Juego juego;
    private static final Map<String, ClienteHandler> clientes = new HashMap<>();  
    private static final Queue<ClienteHandler> colaClientes = new LinkedList<>();

    public ServidorJuego(int filas, int columnas, int port) {
        this.filas = filas;
        this.columnas = columnas;
        this.port = port;
    }
    
    public void iniciar() {
        try (ServerSocket serverSocket = new ServerSocket(port)) {
            System.out.println("Servidor de Juego iniciado en el puerto " + port);
            while (true) { 
                Socket clienteSocket = serverSocket.accept();
                DataInputStream in = new DataInputStream(clienteSocket.getInputStream());
                DataOutputStream out = new DataOutputStream(clienteSocket.getOutputStream());
                String nombreUsuario = in.readUTF();

                out.writeUTF(filas+" "+columnas);
                ClienteHandler cliente = new ClienteHandler(clienteSocket, this, nombreUsuario);
                nuevoCliente(cliente);
                cliente.start();      
            }
        } catch (IOException e) {
        }
    }
    
    public static void nuevoCliente(ClienteHandler cliente){
        synchronized (colaClientes) {
            colaClientes.add(cliente);
            System.out.println("Anadido a la cola: " + cliente.getNickname());
        }
        cliente.enviarMensaje("/ESPERA");
        clientes.put(cliente.getNickname(),cliente);
        if(colaClientes.size() == 2){ // Ya puedo iniciar la partida
            ClienteHandler c1,c2; // Saco los dos primero elementos de la cola
            c1 = colaClientes.poll();
            c2 = colaClientes.poll();
            
            //Me aseguro que no provienen del mismo lugar
            if(c1.getClienteSocket().getInetAddress() != c2.getClienteSocket().getInetAddress() && c1.getClienteSocket().getPort() != c2.getClienteSocket().getPort()){
                c1.enviarMensaje("/COMIENZO " + c2.getNickname());
                c2.enviarMensaje("/COMIENZO " + c1.getNickname());
                juego = new Juego(c1,c2, filas, columnas);
                juego.start();
            }
            else{ // Vuelvo a añadir a la cola unicamente uno de ellos
                System.out.println("los clientes son del miso puerto");
                colaClientes.add(c1);
            }
        }
    }
    
    public void removerCliente(String nickname){
        try{
            ClienteHandler c;
            //Existe el cliente en el mapa de clientes
            if(clientes.containsKey(nickname)){ 
                c = clientes.get(nickname);
                // Su socket está abierto
                if(c.getClienteSocket() != null)  c.getClienteSocket().close();
                
                clientes.remove(nickname);
                
                // El cliente esta en la cola,
                if(colaClientes.contains(c)) colaClientes.remove(c); 
            }

            System.out.println("Cliente <" + nickname + "> desconectado");
        }catch(IOException e){
        }
    }
    
    public void enviarATodos(String msg){
        for (ClienteHandler cliente : clientes.values()) {
            cliente.enviarMensaje(msg);         
        }
    }
    
    public void procesarEntrada(String line, ClienteHandler client) {
        String partes[] = line.split(" ");
        String nickname = client.getNickname();
        
        if(line.startsWith("/CHAT")){
            String newLine = line.replaceFirst("/CHAT", "").trim();
            enviarATodos("/CHAT " + "<" + nickname + ">: " + newLine);
        }
        
        else if(line.startsWith("/JUGADA")){
            int fila = Integer.parseInt(partes[1]);
            int columna = Integer.parseInt(partes[2]);
            String c = partes[3];
            if(juego == null){
                System.out.println("El juego no comenzado");
            }else{
                juego.pieza(fila, columna, c, client);
            }
        }
        
        else if(line.startsWith("/EXIT")){
            enviarATodos("/CHAT <servidor>: Cliente " + nickname + " desconectado");
        }
    }
    
    public static void main(String[] args) {
        Scanner scanner = new Scanner(System.in);
        do{
            System.out.println("Introduce el numero de filas del tablero: ");
            filas = scanner.nextInt();

            System.out.println("Introduce el numero de columnas del tablero: ");
            columnas = scanner.nextInt();
        }while(filas < 3 || filas > 6 || columnas < 3 || columnas > 5);
        ServidorJuego servidorJuego = new ServidorJuego(filas, columnas,12000);
        servidorJuego.iniciar();
    }
    
}
