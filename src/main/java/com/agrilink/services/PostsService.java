package com.agrilink.services;

import com.agrilink.models.Posts;
import com.agrilink.utils.DataSource;

import java.sql.*;
        import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

public class PostsService implements IService<Posts> {
    private Connection connection = DataSource.getInstance().getConnection();

    @Override
    public void ajouter(Posts post) {
        String currentTime = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date());

        String req = "INSERT INTO posts (user_id_id, type, title, description, created_at, status, images) VALUES ('"
                + post.getUser_id_id() + "', '"
                + post.getType() + "', '"
                + post.getTitle() + "', '"
                + post.getDescription() + "', '"
                + currentTime + "', '"
                + post.getStatus() + "', '"
                + post.getImages() + "')";

        try {
            Statement st = connection.createStatement();
            st.executeUpdate(req);
            System.out.println("Post ajouté");
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    public void ajouterPrepared(Posts post) {
        try {
            String req = "INSERT INTO posts (user_id_id, type, title, description, created_at, status, images) VALUES (?, ?, ?, ?, ?, ?, ?)";

            String currentTime = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss").format(new Date());

            PreparedStatement ps = connection.prepareStatement(req);
            ps.setInt(1, post.getUser_id_id());
            ps.setString(2, post.getType());
            ps.setString(3, post.getTitle());
            ps.setString(4, post.getDescription());
            ps.setString(5, currentTime);
            ps.setString(6, post.getStatus());
            ps.setString(7, post.getImages());

            ps.executeUpdate();
            System.out.println("Post ajouté avec prepared statement");
            ps.close();
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    @Override
    public void modifier(Posts post) {
        String req = "UPDATE posts SET user_id_id='" + post.getUser_id_id()
                + "', type='" + post.getType()
                + "', title='" + post.getTitle()
                + "', description='" + post.getDescription()
                + "', status='" + post.getStatus()
                + "', images='" + post.getImages()
                + "' WHERE id=" + post.getId();

        try {
            Statement st = connection.createStatement();
            st.executeUpdate(req);
            System.out.println("Post modifié");
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    public void updateTitle(Posts post) {
        try {
            String req = "UPDATE posts SET title = ? WHERE id = ?";

            PreparedStatement ps = connection.prepareStatement(req);
            ps.setString(1, post.getTitle());
            ps.setInt(2, post.getId());

            ps.executeUpdate();
            System.out.println("Post title updated");
            ps.close();
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    @Override
    public void supprimer(Posts post) {
        String req = "DELETE FROM posts WHERE id=" + post.getId();

        try {
            Statement st = connection.createStatement();
            st.executeUpdate(req);
            System.out.println("Post supprimé");
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    public void delete(int id) {
        try {
            String req = "DELETE FROM posts WHERE id = ?";

            PreparedStatement ps = connection.prepareStatement(req);
            ps.setInt(1, id);

            ps.executeUpdate();
            System.out.println("Post deleted");
            ps.close();
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }
    }

    @Override
    public List<Posts> rechercher() {
        List<Posts> postsList = new ArrayList<>();

        String req = "SELECT * FROM posts";
        try {
            Statement st = connection.createStatement();
            ResultSet rs = st.executeQuery(req);

            while (rs.next()) {
                Posts post = new Posts(
                        rs.getInt("id"),
                        rs.getInt("user_id_id"),
                        rs.getString("type"),
                        rs.getString("title"),
                        rs.getString("description"),
                        rs.getString("created_at"),
                        rs.getString("status"),
                        rs.getString("images")
                );

                postsList.add(post);
            }
        } catch (SQLException e) {
            System.out.println(e.getMessage());
        }

        return postsList;
    }

    public List<Posts> afficherAll() {
        return rechercher(); // Just calls the standard rechercher method
    }
}
