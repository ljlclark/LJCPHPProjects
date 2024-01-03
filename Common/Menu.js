// Copyright(c) Lester J. Clark 2022 - All Rights Reserved
// Menu.js

// ***************
// Standard Menu Functions
class Menu
{
  // The Constructor function.
  constructor()
  {
    this.CurrentOpenMenu = null;
    this.SelectColor = "white";
  }

  // Close the dropdown Menu.
  CloseMenu(eMenu)
  {
    if (eMenu.className != "clsMenuBarItem")
    {
      eMenu.style.visibility = "hidden";
    }
    this.CurrentOpenMenu = null;
  }

  // Highlight the Menu item.
  HighlightItem(eSrc)
  {
    var eMenuBar;
    var sMenuName;

    //document.all.Debug.innerText = "Enter: " + eSrc.className;

    switch (eSrc.className)
    {
      case "clsMenuBarItem":
        {
          var eMenuBarItem = eSrc;
          var eMenu;

          // highlight menubar item
          eMenuBarItem.style.color = this.SelectColor;

          // Get the Menu Bar element.
          eMenuBar = eSrc;
          while (eMenuBar.className != "clsMenuBar")
          {
            eMenuBar = eMenuBar.parentElement;
          }

          // position and display menu
          if (eMenuBar.className == "clsMenuBar")
          {
            sMenuName = eMenuBar.id;

            // Strip the menu bar name portion of the id from the menu bar
            // item to get the menu id and menu element.
            var id = eMenuBarItem.id.replace(sMenuName, "");
            eMenu = Common.Element(id);

            if (this.CurrentOpenMenu && this.CurrentOpenMenu != eMenu)
            {
              this.CloseMenu(this.CurrentOpenMenu);
            }
            if (eMenu)
            {
              this.OpenMenu(eMenuBarItem, eMenu);
            }
          }
        }
        break;

      // MouseOver on a Menu Item.
      case "clsMenuItem":
        {
          var eMenuItem = eSrc;
          if (eSrc.tagName != "HR")
          {
            eMenuItem.style.color = this.SelectColor;
            eMenuItem.style.borderTop = "1px solid #c8e8ff";
            eMenuItem.style.borderLeft = "1px solid #c8e8ff";
            eMenuItem.style.borderRight = "1px solid #4480b8";
            eMenuItem.style.borderBottom = "1px solid #4480b8";
          }
        }
        break;

      case "":
        {
          if (this.CurrentOpenMenu)
          {
            if (eSrc.tagName == "A"
              || false == this.CurrentOpenMenu.contains(eSrc))
            {
              this.CloseMenu(this.CurrentOpenMenu);
            }
          }
        }
        break;
    }
  }

  // Open the dropdown Menu.
  OpenMenu(eSrc, eMenu)
  {
    var eMenuBar;

    // get menu bar element
    eMenuBar = eSrc;
    while (eMenuBar.className != "clsMenuBar")
    {
      eMenuBar = eMenuBar.parentElement;
    }

    // position and display menu
    if (eMenuBar.className == "clsMenuBar")
    {
      var left = eSrc.offsetLeft + eMenuBar.offsetLeft;
      eMenu.style.left = left + "px";
      //eMenu.style.top = eMenuBar.offsetHeight + eMenuBar.offsetTop - 1;

      eMenu.style.visibility = "visible";
      this.CurrentOpenMenu = eMenu;
    }
  }

  // Reset the Menu item color.
  static ResetItemColor(eSrc)
  {
    //document.all.Debug.innerText = "Exit: " + eSrc.className;
    // return menu item to default color
    switch (eSrc.className)
    {
      case "clsMenuBarItem":
      case "clsMenuItem":
        {
          eSrc.style.color = "";
          if (eSrc.tagName != "HR")
          {
            eSrc.style.borderTop = "1px solid #87c7ff";
            eSrc.style.borderLeft = "1px solid #87c7ff";
            eSrc.style.borderRight = "1px solid #87c7ff";
            eSrc.style.borderBottom = "1px solid #87c7ff";
          }
        }
        break;
    }
  }
}
