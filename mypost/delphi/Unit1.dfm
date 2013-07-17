object Form1: TForm1
  Left = 216
  Top = 126
  Width = 924
  Height = 780
  Caption = 'Form1'
  Color = clBtnFace
  Font.Charset = DEFAULT_CHARSET
  Font.Color = clWindowText
  Font.Height = -11
  Font.Name = 'MS Sans Serif'
  Font.Style = []
  OldCreateOrder = False
  OnCreate = FormCreate
  PixelsPerInch = 96
  TextHeight = 13
  object pnl1: TPanel
    Left = 0
    Top = 0
    Width = 908
    Height = 57
    Align = alTop
    Alignment = taRightJustify
    Caption = '1'#12289#22635#20889#20449#24687' 2'#12289#36873#25321'EXCEL 3'#12289#21457#24067#25968#25454
    Font.Charset = DEFAULT_CHARSET
    Font.Color = clHighlight
    Font.Height = -19
    Font.Name = 'MS Sans Serif'
    Font.Style = []
    ParentFont = False
    TabOrder = 0
    object btn1: TButton
      Left = 32
      Top = 16
      Width = 129
      Height = 25
      BiDiMode = bdLeftToRight
      Caption = #9312#21047#26032#37197#32622#39029#38754
      Font.Charset = GB2312_CHARSET
      Font.Color = clHighlight
      Font.Height = -16
      Font.Name = #23435#20307
      Font.Style = []
      ParentBiDiMode = False
      ParentFont = False
      TabOrder = 0
      OnClick = btn1Click
    end
    object btn2: TButton
      Left = 320
      Top = 16
      Width = 97
      Height = 25
      Caption = #9314#21457#24067#25968#25454
      Font.Charset = GB2312_CHARSET
      Font.Color = clHighlight
      Font.Height = -16
      Font.Name = #23435#20307
      Font.Style = []
      ParentFont = False
      TabOrder = 1
      OnClick = btn2Click
    end
    object btn3: TButton
      Left = 176
      Top = 16
      Width = 129
      Height = 25
      Caption = #9313#36873#25321'EXCEL'#25991#20214
      Font.Charset = GB2312_CHARSET
      Font.Color = clHighlight
      Font.Height = -16
      Font.Name = #23435#20307
      Font.Style = []
      ParentFont = False
      TabOrder = 2
      OnClick = btn3Click
    end
  end
  object wb: TEmbeddedWB
    Left = 0
    Top = 57
    Width = 908
    Height = 685
    Align = alClient
    TabOrder = 1
    Silent = False
    DisableCtrlShortcuts = 'N'
    UserInterfaceOptions = [EnablesFormsAutoComplete, EnableThemes]
    About = ' EmbeddedWB http://bsalsa.com/'
    PrintOptions.HTMLHeader.Strings = (
      '<HTML></HTML>')
    PrintOptions.Orientation = poPortrait
    ControlData = {
      4C000000D85D0000CC4600000000000000000000000000000000000000000000
      000000004C000000000000000000000001000000E0D057007335CF11AE690800
      2B2E126208000000000000004C0000000114020000000000C000000000000046
      8000000000000000000000000000000000000000000000000000000000000000
      00000000000000000100000000000000000000000000000000000000}
  end
  object open: TOpenDialog
    Filter = 'Excel'#25991#20214'|*.xls'
    Left = 168
    Top = 160
  end
end
