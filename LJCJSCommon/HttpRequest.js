// Copyright(c) Lester J. Clark 2022 - All Rights Reserved
// HttpRequest.js

// ***************
// Represents an HttpRequest.
class HttpRequest
{
  // Status
  OK = 200;
  RedirectList = 300;

  Request = null;
  CallBack = null;
  Verb = null;
  AllowPrivate = false;
  AllowXMLHttp = false;

  // The Constructor function.
  constructor(allowXMLHttp = false)
  {
    if (true == allowXMLHttp)
    {
      this.AllowXMLHttp = true;
      this.Request = new XMLHttpRequest();
    }
  }

  // Get data with fetch().
  Fetch(sUrl, cFunction)
  {
    fetch(sUrl)
      .then(function (response)
      {
        if (response.ok)
        {
          //return response.json();
          return response.text();
        }
        else
        {
          return Promise.reject({
            status: response.status,
            statusText: response.statusText
          });
        }
      })
      .then(function (data)
      {
        cFunction(data);
      })
      .catch(function (error)
      {
        alert(error);
      });
  }

  // Open and Send the HEAD request.
  GetHead(sUrl, cFunction, useAsync = true)
  {
    if (false == this.AllowXMLHttp)
    {
      return;
    }
    this.CallBack = cFunction;
    this.Request.onload = this._Load.bind(this);
    this.Request.onerror = this._LoadError.bind(this);

    this.Verb = "HEAD";
    this.Request.open(this.Verb, sUrl, useAsync);
    this.AllowPrivate = true;
    this.Request.send();
  }

  // Open and Send the request.
  Send(sUrl, cFunction, useAsync = true, usePost = false, data = null)
  {
    if (false == this.AllowXMLHttp)
    {
      return;
    }
    this.CallBack = cFunction;
    //this.Request.onreadystatechange = this._StatusChange.bind(this);
    this.Request.onload = this._Load.bind(this);
    this.Request.onerror = this._LoadError.bind(this);

    this.Verb = "GET";
    if (true == usePost)
    {
      this.Verb = "POST";
    }
    this.Request.open(this.Verb, sUrl, useAsync);
    this.AllowPrivate = true;
    this.Request.send(data);
  }

  // The Request loaded successfully.
  _Load()
  {
    if (false == this.AllowPrivate)
    {
      return;
    }
    this.AllowPrivate = false;
    try
    {
      //alert(`${this.Request.readyState}-${this.Request.status}`);
      if ("HEAD" == this.Verb)
      {
        this.CallBack(this.Request.getAllResponseHeaders());
      }
      else
      {
        this.CallBack(this.Request.responseText);
      }
    }
    catch (e)
    {
      alert(`Exception: ${e.description}`);
    }
  }

  // The Request had an error.
  _LoadError()
  {
    if (false == this.AllowPrivate)
    {
      return;
    }
    this.AllowPrivate = false;
    alert(`Error: ${this.Request.status}-${this.Request.statusText}`);
  }

  // The Request status change callback.
  _StatusChange()
  {
    if (false == this.AllowPrivate)
    {
      return;
    }
    this.AllowPrivate = false;
    try
    {
      //alert(`${this.Request.readyState}-${this.Request.status}`);
      if (this.Request.DONE == this.Request.readyState)
      {
        if (this.Request.status >= this.OK
          && this.Request.status < this.RedirectList)
        {
          this.CallBack(this.Request.responseText);
        }
        else
        {
          alert(`Error: ${this.Request.status}-${this.Request.statusText}`);
        }
      }
    }
    catch (e)
    {
      alert(`Exception: ${e.description}`);
    }
  }
}