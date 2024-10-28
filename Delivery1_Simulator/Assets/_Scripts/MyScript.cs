using System;
using System.Collections;
using System.Collections.Generic;
using UnityEngine;
using UnityEngine.Networking;

public class MyScript : MonoBehaviour
{
    uint currentUserId;
    uint currentSessionId;
    uint currentPurchaseId;

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

    IEnumerator UploadPlayer(string name, string country, int age, float gender, DateTime date)
    {
        WWWForm form = new WWWForm();
        form.AddField("name", name);
        form.AddField("country", country);
        form.AddField("age", age.ToString());
        form.AddField("gender", gender.ToString(System.Globalization.CultureInfo.InvariantCulture));
        form.AddField("date", date.ToString("yyyy-MM-dd HH:mm:ss"));

        using (UnityWebRequest www = UnityWebRequest.Post("https://citmalumnes.upc.es/~antoniorr14/Player_Data.php", form))
        {
            yield return www.SendWebRequest();

            if (www.result != UnityWebRequest.Result.Success)
            {
                UnityEngine.Debug.LogError("Player data upload failed: " + www.error);
            }
            else
            {
                string answer = www.downloadHandler.text.Trim(new char[] { '\uFEFF', '\u200B', ' ', '\t', '\r', '\n' });
                if (uint.TryParse(answer, out uint parsedId) && parsedId > 0)
                {
                    currentUserId = parsedId;
                    CallbackEvents.OnAddPlayerCallback.Invoke(currentUserId);
                }
                else
                {
                    UnityEngine.Debug.LogError("Invalid user ID received: " + answer);
                }
            }
        }
    }

    IEnumerator UploadStartSession(DateTime date, uint playerID)
    {
        if (currentUserId == 0)
        {
            UnityEngine.Debug.LogError("User ID is not set, cannot start session");
            yield break;  // Salir de la coroutine si no hay un UserId válido
        }

        WWWForm form = new WWWForm();
        form.AddField("UserId", currentUserId.ToString());
        form.AddField("StartSession", date.ToString("yyyy-MM-dd HH:mm:ss"));

        string url = "https://citmalumnes.upc.es/~antoniorr14/NewSession.php";
        UnityWebRequest www = UnityWebRequest.Post(url, form);
        yield return www.SendWebRequest();

        if (www.result == UnityWebRequest.Result.Success)
        {
            string answer = www.downloadHandler.text.Trim(new char[] { '\uFEFF', '\u200B', ' ', '\t', '\r', '\n' });
            if (uint.TryParse(answer, out uint parsedId) && parsedId > 0)
            {
                currentSessionId = parsedId;
                CallbackEvents.OnNewSessionCallback.Invoke(currentSessionId);
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


    IEnumerator UploadEndSession(DateTime date, uint playerID)
    {
        WWWForm form = new WWWForm();
        form.AddField("User_ID", currentUserId.ToString());
        form.AddField("End_Session", date.ToString("yyyy-MM-dd HH:mm:ss"));
        form.AddField("Session_ID", currentSessionId.ToString());

        string url = "https://citmalumnes.upc.es/~hangx/Close_Session_Data.php";
        UnityWebRequest www = UnityWebRequest.Post(url, form);
        yield return www.SendWebRequest();

        if (www.result == UnityWebRequest.Result.Success)
        {
            CallbackEvents.OnEndSessionCallback.Invoke(currentSessionId);
        }
        else
        {
            UnityEngine.Debug.LogError("Session end data upload failed: " + www.error);
        }

    }


    IEnumerator UploadItem(int item, DateTime date, uint playerID)
    {
        WWWForm form = new WWWForm();
        form.AddField("Item", item.ToString());
        form.AddField("User_ID", currentUserId.ToString());
        form.AddField("Session_ID", currentSessionId.ToString());
        form.AddField("Buy_Date", date.ToString("yyyy-MM-dd HH:mm:ss"));

        UnityWebRequest www = UnityWebRequest.Post("https://citmalumnes.upc.es/~antoniorr14/Purchase_Data.php", form);
        yield return www.SendWebRequest();

        if (www.result != UnityWebRequest.Result.Success)
        {
            UnityEngine.Debug.LogError("Purchase data upload failed: " + www.error);
        }
        else
        {
            string answer = www.downloadHandler.text.Trim(new char[] { '\uFEFF', '\u200B', ' ', '\t', '\r', '\n' });
            if (uint.TryParse(answer, out uint parsedId) && parsedId > 0)
            {
                currentPurchaseId = parsedId;
                CallbackEvents.OnItemBuyCallback.Invoke(playerID);
            }
            else
            {
                UnityEngine.Debug.LogError("Invalid purchase ID received: " + www.downloadHandler.text);
            }
        }
    }
}
