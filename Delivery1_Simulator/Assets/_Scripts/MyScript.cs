using System;
using System.Collections;
using System.Collections.Generic;
using UnityEngine;

public class MyScript : MonoBehaviour
{
    // Start is called before the first frame update
    void Start()
    {
        Simulator.OnNewPlayer += NewPlayer();
        Simulator.OnNewSession += NewSession();
        Simulator.OnBuyItem += BuyItem();
        Simulator.OnEndSession += EndSession();
    }

    private Action<DateTime, uint> EndSession()
    {
        throw new NotImplementedException();
    }

    private Action<int, DateTime, uint> BuyItem()
    {
        throw new NotImplementedException();
    }

    private Action<DateTime, uint> NewSession()
    {
        throw new NotImplementedException();
    }

    private Action<string, string, int, float, DateTime> NewPlayer()
    {
        throw new NotImplementedException();
    }

    // Update is called once per frame
    void Update()
    {
        
    }

    IEnumerator Upload()
    {
        List<IMultipartFormSection> formtData = new List<IMultipartFormSection>
    }
}
