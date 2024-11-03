using System;
using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.Networking;

public class MyScript : MonoBehaviour
{
    uint currentUserId;    // Stores the ID of the current user
    uint currentSessionId; // Stores the ID of the current session
    uint currentPurchaseId; // Stores the ID of the current purchase

    private void OnEnable()
    {
        Simulator.OnNewPlayer += HandleNewPlayer;
        Simulator.OnNewSession += HandleNewSession;
        Simulator.OnEndSession += HandleEndSession;
        Simulator.OnBuyItem += HandleBuyItem;
    }

    private void OnDisable()
    {
        Simulator.OnNewPlayer -= HandleNewPlayer;
        Simulator.OnNewSession -= HandleNewSession;
        Simulator.OnEndSession -= HandleEndSession;
        Simulator.OnBuyItem -= HandleBuyItem;
    }

    private void HandleNewPlayer(string name, string country, int age, float gender, DateTime date)
    {
        StartCoroutine(UploadPlayer(name, country, age, gender, date));
    }

    private void HandleNewSession(DateTime date, uint playerID)
    {
        StartCoroutine(UploadStartSession(date, playerID));
    }

    private void HandleEndSession(DateTime date, uint playerID)
    {
        StartCoroutine(UploadEndSession(date, playerID));
    }

    private void HandleBuyItem(int item, DateTime date, uint playerID)
    {
        StartCoroutine(UploadItem(item, date, playerID));
    }

    // Coroutine to upload player data to the server
    IEnumerator UploadPlayer(string name, string country, int age, float gender, DateTime date)
    {
        // Create a form with the necessary fields
        WWWForm form = new WWWForm();
        form.AddField("name", name);
        form.AddField("country", country);
        form.AddField("age", age.ToString());
        form.AddField("gender", gender.ToString(System.Globalization.CultureInfo.InvariantCulture));
        form.AddField("date", date.ToString("yyyy-MM-dd HH:mm:ss"));

        // Send form data to the server
        using (UnityWebRequest www = UnityWebRequest.Post("https://citmalumnes.upc.es/~antoniorr14/Player_Data.php", form))
        {
            yield return www.SendWebRequest();

            // Check for success or error in the response
            if (www.result != UnityWebRequest.Result.Success)
            {
                UnityEngine.Debug.LogError("Player data upload failed: " + www.error);
            }
            else
            {
                // Parse the response text to retrieve the player ID
                string answer = www.downloadHandler.text.Trim(new char[] { '\uFEFF', '\u200B', ' ', '\t', '\r', '\n' });
                if (uint.TryParse(answer, out uint parsedId) && parsedId > 0)
                {
                    currentUserId = parsedId;  // Set the current user ID
                    CallbackEvents.OnAddPlayerCallback.Invoke(currentUserId);  // Trigger callback with new player ID
                    Debug.Log("Uploaded Player with ID: " + currentUserId);
                }
                else
                {
                    UnityEngine.Debug.LogError("Invalid user ID received: " + answer);
                }
            }
        }
    }

    // Coroutine to upload session start data to the server
    IEnumerator UploadStartSession(DateTime date, uint playerID)
    {
        // Check if currentUserId is valid before starting session
        if (currentUserId == 0)
        {
            UnityEngine.Debug.LogError("User ID is not set, cannot start session");
            yield break;  // Exit coroutine if UserId is not valid
        }

        // Create a form with the necessary fields
        WWWForm form = new WWWForm();
        form.AddField("UserId", currentUserId.ToString());
        form.AddField("StartSession", date.ToString("yyyy-MM-dd HH:mm:ss"));

        // URL to post the data to
        string url = "https://citmalumnes.upc.es/~antoniorr14/NewSession.php";
        UnityWebRequest www = UnityWebRequest.Post(url, form);
        yield return www.SendWebRequest();

        // Check for success or error in the response
        if (www.result == UnityWebRequest.Result.Success)
        {
            // Parse the response text to retrieve the session ID
            string answer = www.downloadHandler.text.Trim(new char[] { '\uFEFF', '\u200B', ' ', '\t', '\r', '\n' });
            if (uint.TryParse(answer, out uint parsedId) && parsedId > 0)
            {
                currentSessionId = parsedId;  // Set the current session ID
                CallbackEvents.OnNewSessionCallback.Invoke(currentSessionId);  // Trigger callback with new session ID
            }
            else
            {
                UnityEngine.Debug.LogError("Invalid session ID received: " + answer);
            }
        }
        else
        {
            UnityEngine.Debug.LogError("Session start data upload failed: " + www.error);
        }
    }

    // Coroutine to upload session end data to the server
    IEnumerator UploadEndSession(DateTime date, uint playerID)
    {
        // Create a form with the necessary fields
        WWWForm form = new WWWForm();
        form.AddField("User_Id", currentUserId.ToString());
        form.AddField("End_Session", date.ToString("yyyy-MM-dd HH:mm:ss"));
        form.AddField("Session_ID", currentSessionId.ToString());

        // URL to post the data to
        string url = "https://citmalumnes.upc.es/~antoniorr14/CloseSession.php";
        UnityWebRequest www = UnityWebRequest.Post(url, form);
        yield return www.SendWebRequest();

        // Check for success or error in the response
        if (www.result == UnityWebRequest.Result.Success)
        {
            CallbackEvents.OnEndSessionCallback.Invoke(currentSessionId);  // Trigger callback for session end
        }
        else
        {
            UnityEngine.Debug.LogError("Session end data upload failed: " + www.error);
        }
    }

    // Coroutine to upload purchase data to the server
    IEnumerator UploadItem(int item, DateTime date, uint playerID)
    {
        // Create a form with the necessary fields
        WWWForm form = new WWWForm();
        form.AddField("Item", item.ToString());
        form.AddField("Session_ID", currentSessionId.ToString());
        form.AddField("Buy_Date", date.ToString("yyyy-MM-dd HH:mm:ss"));

        // URL to post the data to
        UnityWebRequest www = UnityWebRequest.Post("https://citmalumnes.upc.es/~antoniorr14/Purchase_Data.php", form);
        yield return www.SendWebRequest();

        // Check for success or error in the response
        if (www.result != UnityWebRequest.Result.Success)
        {
            UnityEngine.Debug.LogError("Purchase data upload failed: " + www.error);
        }
        else
        {
            // Parse the response text to retrieve the purchase ID
            string answer = www.downloadHandler.text.Trim(new char[] { '\uFEFF', '\u200B', ' ', '\t', '\r', '\n' });
            if (uint.TryParse(answer, out uint parsedId) && parsedId > 0)
            {
                currentPurchaseId = parsedId;  // Set the current purchase ID
                CallbackEvents.OnItemBuyCallback.Invoke(playerID);  // Trigger callback for item purchase
            }
            else
            {
                UnityEngine.Debug.LogError("Invalid purchase ID received: " + www.downloadHandler.text + "ID: " + parsedId);
            }
        }
    }
}
