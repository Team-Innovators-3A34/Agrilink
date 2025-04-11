package com.agrilink;

import com.agrilink.models.Posts;
import com.agrilink.services.PostsService;

import java.util.List;

public class TestPosts {
    public static void main(String[] args) {
        PostsService postsService = new PostsService();

        // Create a new post to add
        /*
        Posts newPost = new Posts(
                1,
                "article",
                "Test Post",
                "This is a test post description",
                "active",
                "image1.jpg"
        );
        postsService.ajouter(newPost);*/

        // Test adding a post with PreparedStatement
        Posts anotherPost = new Posts(
                1,
                "question",
                "Another Test",
                "This is another test post",
                "active",
                "image2.jpg"
        );

        postsService.ajouterPrepared(anotherPost);

        //all posts
        System.out.println("\nAll posts in database:");
        List<Posts> allPosts = postsService.rechercher();
        for (Posts post : allPosts) {
            System.out.println(post);
        }

        // Test updating a post title (first post in the list)
        if (!allPosts.isEmpty()) {
            Posts postToUpdate = allPosts.get(0);
            postToUpdate.setTitle("Updated Title");
            postsService.updateTitle(postToUpdate);
            System.out.println("Post title updated");
        }

        // Test deleting a post
        /*
        if (!allPosts.isEmpty()) {
            Posts postToDelete = allPosts.get(allPosts.size() - 1);
            postsService.supprimer(postToDelete);
            System.out.println("Post with ID " + postToDelete.getId() + " deleted");
        }
        */
    }
}