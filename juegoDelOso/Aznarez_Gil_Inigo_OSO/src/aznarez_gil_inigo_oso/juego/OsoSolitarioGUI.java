/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/GUIForms/JFrame.java to edit this template
 */
package aznarez_gil_inigo_oso.juego;

import java.awt.Font;
import java.awt.GridLayout;
import java.awt.event.ActionEvent;
import java.awt.event.ActionListener;
import java.awt.event.WindowEvent;
import javax.swing.JButton;
import javax.swing.JDialog;
import javax.swing.JLabel;
import javax.swing.JOptionPane;
import javax.swing.JPanel;

/**
 *
 * @author Iñigo Aznarez
 */
public class OsoSolitarioGUI extends javax.swing.JFrame {
    private JButton [][] matriz_botones;
    private int rows;
    private int cols;
    private int contOsos = 0;
    private Tablero tablero;
    
    /**
     * Creates new form GUIJuego2
     */
    public OsoSolitarioGUI() {
        initComponents();
        setLocationRelativeTo(null); // Centrar el JFrame en la pantalla
    }

    /**
     * This method is called from within the constructor to initialize the form.
     * WARNING: Do NOT modify this code. The content of this method is always
     * regenerated by the Form Editor.
     */
    @SuppressWarnings("unchecked")
    // <editor-fold defaultstate="collapsed" desc="Generated Code">//GEN-BEGIN:initComponents
    private void initComponents() {

        buttonIniciarPartida = new javax.swing.JButton();
        jPanelTablero = new javax.swing.JPanel();
        editFilas = new javax.swing.JTextField();
        editColumnas = new javax.swing.JTextField();
        etiquetaOsos = new javax.swing.JLabel();
        contadorOsos = new javax.swing.JLabel();
        buttonSalir = new javax.swing.JButton();

        setDefaultCloseOperation(javax.swing.WindowConstants.EXIT_ON_CLOSE);

        buttonIniciarPartida.setText("Iniciar Partida");
        buttonIniciarPartida.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                buttonIniciarPartidaActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout jPanelTableroLayout = new javax.swing.GroupLayout(jPanelTablero);
        jPanelTablero.setLayout(jPanelTableroLayout);
        jPanelTableroLayout.setHorizontalGroup(
            jPanelTableroLayout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGap(0, 0, Short.MAX_VALUE)
        );
        jPanelTableroLayout.setVerticalGroup(
            jPanelTableroLayout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGap(0, 160, Short.MAX_VALUE)
        );

        editFilas.setText("Filas");
        editFilas.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                editFilasMouseClicked(evt);
            }
        });

        editColumnas.setText("Columnas");
        editColumnas.addMouseListener(new java.awt.event.MouseAdapter() {
            public void mouseClicked(java.awt.event.MouseEvent evt) {
                editColumnasMouseClicked(evt);
            }
        });

        etiquetaOsos.setText("Osos:");

        contadorOsos.setText("0");

        buttonSalir.setText("Salir");
        buttonSalir.addActionListener(new java.awt.event.ActionListener() {
            public void actionPerformed(java.awt.event.ActionEvent evt) {
                buttonSalirActionPerformed(evt);
            }
        });

        javax.swing.GroupLayout layout = new javax.swing.GroupLayout(getContentPane());
        getContentPane().setLayout(layout);
        layout.setHorizontalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addGap(19, 19, 19)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING, false)
                    .addGroup(layout.createSequentialGroup()
                        .addComponent(buttonSalir)
                        .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.RELATED, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                        .addComponent(etiquetaOsos)
                        .addGap(18, 18, 18)
                        .addComponent(contadorOsos)
                        .addGap(55, 55, 55))
                    .addGroup(layout.createSequentialGroup()
                        .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.TRAILING, false)
                            .addComponent(jPanelTablero, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                            .addGroup(layout.createSequentialGroup()
                                .addComponent(editFilas, javax.swing.GroupLayout.PREFERRED_SIZE, 71, javax.swing.GroupLayout.PREFERRED_SIZE)
                                .addGap(18, 18, 18)
                                .addComponent(editColumnas, javax.swing.GroupLayout.PREFERRED_SIZE, 71, javax.swing.GroupLayout.PREFERRED_SIZE)
                                .addGap(40, 40, 40)
                                .addComponent(buttonIniciarPartida)))
                        .addGap(88, 88, 88))))
        );
        layout.setVerticalGroup(
            layout.createParallelGroup(javax.swing.GroupLayout.Alignment.LEADING)
            .addGroup(layout.createSequentialGroup()
                .addGap(48, 48, 48)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(buttonIniciarPartida)
                    .addComponent(editFilas, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE)
                    .addComponent(editColumnas, javax.swing.GroupLayout.PREFERRED_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.PREFERRED_SIZE))
                .addGap(18, 18, 18)
                .addComponent(jPanelTablero, javax.swing.GroupLayout.DEFAULT_SIZE, javax.swing.GroupLayout.DEFAULT_SIZE, Short.MAX_VALUE)
                .addPreferredGap(javax.swing.LayoutStyle.ComponentPlacement.UNRELATED)
                .addGroup(layout.createParallelGroup(javax.swing.GroupLayout.Alignment.BASELINE)
                    .addComponent(etiquetaOsos)
                    .addComponent(contadorOsos)
                    .addComponent(buttonSalir))
                .addGap(16, 16, 16))
        );

        pack();
    }// </editor-fold>//GEN-END:initComponents

    private void editFilasMouseClicked(java.awt.event.MouseEvent evt) {//GEN-FIRST:event_editFilasMouseClicked
        // TODO add your handling code here:
        editFilas.setText("");
    }//GEN-LAST:event_editFilasMouseClicked

    private void editColumnasMouseClicked(java.awt.event.MouseEvent evt) {//GEN-FIRST:event_editColumnasMouseClicked
        // TODO add your handling code here:
        editColumnas.setText("");
    }//GEN-LAST:event_editColumnasMouseClicked

    private void buttonSalirActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_buttonSalirActionPerformed
        // TODO add your handling code here:
        OsoSolitarioGUI.this.dispatchEvent(new WindowEvent(OsoSolitarioGUI.this, WindowEvent.WINDOW_CLOSING));
    }//GEN-LAST:event_buttonSalirActionPerformed

    private void buttonIniciarPartidaActionPerformed(java.awt.event.ActionEvent evt) {//GEN-FIRST:event_buttonIniciarPartidaActionPerformed
       // TODO add your handling code here:
       iniciarTablero();
    }//GEN-LAST:event_buttonIniciarPartidaActionPerformed

    /**
     * @param args the command line arguments
     */
    public static void main(String args[]) {
        /* Set the Nimbus look and feel */
        //<editor-fold defaultstate="collapsed" desc=" Look and feel setting code (optional) ">
        /* If Nimbus (introduced in Java SE 6) is not available, stay with the default look and feel.
         * For details see http://download.oracle.com/javase/tutorial/uiswing/lookandfeel/plaf.html 
         */
        try {
            for (javax.swing.UIManager.LookAndFeelInfo info : javax.swing.UIManager.getInstalledLookAndFeels()) {
                if ("Nimbus".equals(info.getName())) {
                    javax.swing.UIManager.setLookAndFeel(info.getClassName());
                    break;
                }
            }
        } catch (ClassNotFoundException ex) {
            java.util.logging.Logger.getLogger(OsoSolitarioGUI.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (InstantiationException ex) {
            java.util.logging.Logger.getLogger(OsoSolitarioGUI.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (IllegalAccessException ex) {
            java.util.logging.Logger.getLogger(OsoSolitarioGUI.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        } catch (javax.swing.UnsupportedLookAndFeelException ex) {
            java.util.logging.Logger.getLogger(OsoSolitarioGUI.class.getName()).log(java.util.logging.Level.SEVERE, null, ex);
        }
        //</editor-fold>
        //</editor-fold>
        //</editor-fold>
        //</editor-fold>
        //</editor-fold>
        //</editor-fold>
        //</editor-fold>
        //</editor-fold>

        /* Create and display the form */
        java.awt.EventQueue.invokeLater(new Runnable() {
            public void run() {
                new OsoSolitarioGUI().setVisible(true);
            }
        });
    }

    // Variables declaration - do not modify//GEN-BEGIN:variables
    private javax.swing.JButton buttonIniciarPartida;
    private javax.swing.JButton buttonSalir;
    private javax.swing.JLabel contadorOsos;
    private javax.swing.JTextField editColumnas;
    private javax.swing.JTextField editFilas;
    private javax.swing.JLabel etiquetaOsos;
    private javax.swing.JPanel jPanelTablero;
    // End of variables declaration//GEN-END:variables


    private void iniciarTablero() {
        jPanelTablero.removeAll();
      
        String filasStr = editFilas.getText();
        String columnasStr = editColumnas.getText();
        rows = Integer.parseInt(filasStr);
        cols = Integer.parseInt(columnasStr);
        tablero = new Tablero(rows,cols);        
        jPanelTablero.setLayout(new GridLayout(rows,cols));
        matriz_botones = new JButton[rows][cols]; // Inicializa la matriz de botones
        
        for(int x=0; x < rows; x++) {
            for (int y = 0; y < cols; y++) {
                final int i = x;
                final int j = y;
                JButton botonAux = new JButton(" ");
                botonAux.setPreferredSize(new java.awt.Dimension(50, 50));
                botonAux.setFont(new Font("Arial", Font.PLAIN, 20));
                botonAux.addActionListener(new ActionListener() {
                    @Override
                    public void actionPerformed(ActionEvent ae) {
                        Object[] opciones = {"S", "O", "Cancelar"};
                        int seleccion = JOptionPane.showOptionDialog(null, "Selecciona una opción para la letra", "Selección de letra",
                                JOptionPane.DEFAULT_OPTION, JOptionPane.INFORMATION_MESSAGE, null, opciones, opciones[0]);
                        switch (seleccion) {
                            case 0:
                                botonAux.setText("S");
                                botonAux.setEnabled(false);
                                tablero.colocarCaracter(i, j, "S");
                                comprobarOso(i,j);
                                break;
                            case 1:
                                botonAux.setEnabled(false);
                                botonAux.setText("O");
                                tablero.colocarCaracter(i, j, "O");
                                comprobarOso(i,j);
                                break;
                            default:
                                break;
                        }
                    }
                });
                matriz_botones[x][y] =  botonAux;
                jPanelTablero.add(botonAux);
                jPanelTablero.revalidate();
            }
        }
        this.pack();
    }
    
    private void mostrarAvisoFin() {
        JDialog avisoFin = new JDialog();
        JPanel panel = new JPanel();
        JLabel etiqueta = new JLabel("Fin de la partida");
        JButton botonSalir = new JButton("Salir");
        botonSalir.addActionListener((e) -> {
            dispatchEvent(new WindowEvent(this, WindowEvent.WINDOW_CLOSING));
        });
        panel.add(etiqueta);
        panel.add(botonSalir);
        avisoFin.add(panel);
        avisoFin.pack();
        avisoFin.setLocationRelativeTo(null);
        avisoFin.setVisible(true);
    }

    private void comprobarOso(int i, int j) {
        if(tablero.esOSO(i, j)){
            sumarContador();
        }
        if(tablero.esFinal()){
            mostrarAvisoFin();
        }
    }
    
    private void sumarContador(){
        contOsos++;
        String contOsosStr = String.valueOf(contOsos);
        contadorOsos.setText(contOsosStr);
    }
}
